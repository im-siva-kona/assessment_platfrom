import cv2
import numpy as np
import mediapipe as mp
import pyaudio
import wave
import threading
import time
import logging
from datetime import datetime
import json

class AIProctoringSystem:
    def __init__(self):
        # Initialize MediaPipe components for face detection and pose estimation
        self.mp_face_detection = mp.solutions.face_detection
        self.mp_face_mesh = mp.solutions.face_mesh
        self.face_detection = self.mp_face_detection.FaceDetection(min_detection_confidence=0.5)
        self.face_mesh = self.mp_face_mesh.FaceMesh(
            min_detection_confidence=0.5,
            min_tracking_confidence=0.5
        )
        
        # Initialize logging
        logging.basicConfig(
            filename=f'proctor_log_{datetime.now().strftime("%Y%m%d_%H%M%S")}.log',
            level=logging.INFO,
            format='%(asctime)s - %(levelname)s - %(message)s'
        )
        
        # Initialize violation counters
        self.violations = {
            'no_face_detected': 0,
            'multiple_faces': 0,
            'looking_away': 0,
            'audio_anomaly': 0
        }
        
        # Audio settings
        self.CHUNK = 1024
        self.FORMAT = pyaudio.paFloat32
        self.CHANNELS = 1
        self.RATE = 44100
        self.audio_threshold = 0.1
        
    def start_monitoring(self):
        """Start the proctoring session with both video and audio monitoring"""
        try:
            # Start video monitoring in a separate thread
            video_thread = threading.Thread(target=self.monitor_video)
            audio_thread = threading.Thread(target=self.monitor_audio)
            
            video_thread.start()
            audio_thread.start()
            
            # Join threads
            video_thread.join()
            audio_thread.join()
            
        except Exception as e:
            logging.error(f"Error in monitoring: {str(e)}")
            raise
    
    def monitor_video(self):
        """Monitor video feed for potential violations"""
        cap = cv2.VideoCapture(0)
        
        try:
            while cap.isOpened():
                success, frame = cap.read()
                if not success:
                    logging.error("Failed to read from camera")
                    break
                
                # Convert to RGB for MediaPipe
                frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                
                # Detect faces
                face_results = self.face_detection.process(frame_rgb)
                
                if not face_results.detections:
                    self.log_violation('no_face_detected')
                elif len(face_results.detections) > 1:
                    self.log_violation('multiple_faces')
                else:
                    # Process face mesh for eye tracking
                    mesh_results = self.face_mesh.process(frame_rgb)
                    if mesh_results.multi_face_landmarks:
                        self.check_eye_direction(mesh_results.multi_face_landmarks[0])
                
                # Add monitoring indicators to frame
                self.draw_monitoring_status(frame)
                
                # Display the frame (in development/testing)
                cv2.imshow('AI Proctor View', frame)
                if cv2.waitKey(1) & 0xFF == ord('q'):
                    break
                
        finally:
            cap.release()
            cv2.destroyAllWindows()
    
    def monitor_audio(self):
        """Monitor audio for unusual sounds or conversations"""
        audio = pyaudio.PyAudio()
        
        try:
            stream = audio.open(
                format=self.FORMAT,
                channels=self.CHANNELS,
                rate=self.RATE,
                input=True,
                frames_per_buffer=self.CHUNK
            )
            
            while True:
                data = np.frombuffer(stream.read(self.CHUNK), dtype=np.float32)
                audio_level = np.abs(data).mean()
                
                if audio_level > self.audio_threshold:
                    self.log_violation('audio_anomaly')
                
                time.sleep(0.1)
                
        finally:
            stream.stop_stream()
            stream.close()
            audio.terminate()
    
    def check_eye_direction(self, face_landmarks):
        """Analyze eye direction using facial landmarks"""
        # Get eye landmarks
        left_eye = face_landmarks.landmark[33]  # Left eye center
        right_eye = face_landmarks.landmark[263]  # Right eye center
        
        # Check if eyes are looking too far from center
        if abs(left_eye.x - 0.5) > 0.2 or abs(right_eye.x - 0.5) > 0.2:
            self.log_violation('looking_away')
    
    def log_violation(self, violation_type):
        """Log violations and update counters"""
        self.violations[violation_type] += 1
        logging.warning(f"Violation detected: {violation_type}")
        
        # Save violation data
        violation_data = {
            'timestamp': datetime.now().isoformat(),
            'type': violation_type,
            'count': self.violations[violation_type]
        }
        
        with open('violations.json', 'a') as f:
            json.dump(violation_data, f)
            f.write('\n')
    
    def draw_monitoring_status(self, frame):
        """Draw monitoring status indicators on frame"""
        status_text = "Monitoring Active"
        cv2.putText(frame, status_text, (10, 30), 
                   cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
        
        # Draw violation counters
        y_pos = 60
        for violation, count in self.violations.items():
            text = f"{violation}: {count}"
            cv2.putText(frame, text, (10, y_pos), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)
            y_pos += 20

def main():
    """Main function to run the proctoring system"""
    try:
        proctor = AIProctoringSystem()
        print("Starting AI Proctoring System...")
        proctor.start_monitoring()
    except KeyboardInterrupt:
        print("\nStopping AI Proctoring System...")
    except Exception as e:
        print(f"Error: {str(e)}")
        logging.error(f"Critical error: {str(e)}")

if __name__ == "__main__":
    main()