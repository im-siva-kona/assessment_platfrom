-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2021 at 06:31 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_eval`
--

-- --------------------------------------------------------

--
-- Table structure for table `atmpt_list`
--

CREATE TABLE `atmpt_list` (
  `id` int(100) NOT NULL,
  `exid` int(100) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `nq` int(100) NOT NULL,
  `cnq` int(100) NOT NULL,
  `ptg` int(100) NOT NULL,
  `status` int(10) NOT NULL,
  `subtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `exm_list`
--

CREATE TABLE `exm_list` (
  `exid` int(100) NOT NULL,
  `exname` varchar(100) NOT NULL,
  `nq` int(50) NOT NULL,
  `desp` varchar(100) NOT NULL,
  `subt` datetime NOT NULL,
  `extime` datetime NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subject` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO EXM_LIST VALUES (20, 'computer science', 2, 'exams', '2024-11-05 14:54:00', '2024-11-04 14:54:00', '2024-11-04 14:54:30', 'COMPUTER SCIENCE', NULL, NULL, 'active');
-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
    `1st_init` INT(1) NOT NULL,
    `thame` VARCHAR(100) NOT NULL,
    `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    `feedback` VARCHAR(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `fname`, `date`, `feedback`) VALUES
(5, 'Teacher Rosey', '2021-12-12 13:01:00', 'Please kindly complete all the homework and submit tomorrow '),
(6, 'Teacher Rosey', '2021-12-13 06:23:18', 'Hello this is an annoucement');

-- --------------------------------------------------------

--
-- Table structure for table `qstn_list`
--

CREATE TABLE `qstn_list` (
  `exid` int(11) NOT NULL,
  `qid` int(11) NOT NULL,
  `qstn` varchar(200) NOT NULL,
  `qstn_o1` varchar(100) NOT NULL,
  `qstn_o2` varchar(100) NOT NULL,
  `qstn_o3` varchar(100) NOT NULL,
  `qstn_o4` varchar(100) NOT NULL,
  `qstn_ans` varchar(100) NOT NULL,
  `sno` int(20) NOT NULL,
  `qstn_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `pword` varchar(255) NOT NULL,
  `fname` char(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` char(10) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `uname`, `pword`, `fname`, `dob`, `gender`, `email`) VALUES
(10, 'anniefrank', '1f9a884da469fdf263c098fc46891c04', 'Annie Frank', '1889-02-12', 'F', 'anniefrn@yahoo.com'),
(11, 'abraham', '1f9a884da469fdf263c098fc46891c04', 'Abraham Lincoln', '1998-02-12', 'M', 'abraham@usa.com'),
(12, 'mariealx', 'f6fdffe48c908deb0f4c3bd36c032e72', 'Marie Alex', '1790-12-12', 'F', 'mariealex@aol.com');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `pword` varchar(255) NOT NULL,
  `fname` char(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` char(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`id`, `uname`, `pword`, `fname`, `dob`, `gender`, `email`, `subject`) VALUES
(1, 'teacher', '8d788385431273d11e8b43bb78f3aa41', 'Jack Rosso', '2021-12-01', 'M', 'teacher@teach.com', 'CHEMISTRY'),
(2, 'teacher2', 'ccffb0bb993eeb79059b31e1611ec353', 'Rosey', '2021-12-01', 'F', 'teacher@gmail.com', 'BIOLOGY');

CREATE TABLE `ADMIN` (
  `id` INT NOT NULL PRIMARY KEY,
  `uname` VARCHAR(255) NOT NULL,
  `pword` VARCHAR(255) NOT NULL,
  `fname` VARCHAR(255) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` VARCHAR(1) NOT NULL,
  `email` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ADMIN (`id`, `uname`, `pword`, `fname`, `dob`, `gender`, `email`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', '2021-12-01', 'M', 'admin@gmail.com');

-- Table structure for table `report_templates`
CREATE TABLE `report_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `metrics` text NOT NULL,
  `filters` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `analytics_data`
CREATE TABLE `analytics_data` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `comparison_data`
CREATE TABLE `comparison_data` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `value1` varchar(100) NOT NULL,
  `value2` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `api_settings` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `api_key` VARCHAR(255) NOT NULL,
  `api_secret` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `api_settings` (`api_key`, `api_secret`) VALUES
('default_api_key', 'default_api_secret');

CREATE TABLE `user_roles` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` VARCHAR(50) NOT NULL,
  `permissions` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user_roles` (`role`, `permissions`) VALUES
('admin', 'view_dashboard,manage_users,manage_assessments,view_reports'),
('teacher', 'view_dashboard,manage_assessments,view_reports'),
('student', 'view_dashboard,view_reports');

CREATE TABLE HELP_REQUESTS (
  ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  NAME VARCHAR(255) NOT NULL,
  EMAIL VARCHAR(300) NOT NULL ,
  MESSAGE VARCHAR(3000) not NULL,
  CREARED_AT VARCHAR(255) NOT NULL
);

CREATE TABLE PROCTORING_LOGS(
  ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  EXAM_ID VARCHAR(50) INDEX,
  USER_ID VARCHAR(50) INDEX,
  VIOLATION_TYPE VARCHAR(50),
  DETAILS TEXT,
  `TIMESTAMP` DATETIME,
  SEVERITY VARCHAR(20)
);

CREATE TABLE code_submissions (
    submission_id INT PRIMARY KEY AUTO_INCREMENT,
    qid VARCHAR(255) NOT NULL,
    exid VARCHAR(255) NOT NULL,
    uname VARCHAR(255) NOT NULL,
    submitted_code TEXT NOT NULL,
    submission_time DATETIME NOT NULL,
    execution_output TEXT,
    passed_tests BOOLEAN,
    execution_time FLOAT,
    memory_used VARCHAR(50),
    FOREIGN KEY (qid) REFERENCES qstn_list(qid),
    FOREIGN KEY (exid) REFERENCES exm_list(exid),
    INDEX idx_user_exam (uname, exid)
);

-- Indexes for dumped tables
ALTER TABLE `report_templates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `analytics_data`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `comparison_data`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT for dumped tables
ALTER TABLE `report_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `analytics_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `comparison_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Indexes for dumped tables
--

--
-- Indexes for table `atmpt_list`
--
ALTER TABLE `atmpt_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exm_list`
--
ALTER TABLE `exm_list`
  ADD PRIMARY KEY (`exid`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qstn_list`
--
ALTER TABLE `qstn_list`
  ADD PRIMARY KEY (`qid`);

ALTER TABLE QSTN_LIST
 ADD QUESTION mediumtext;

ALTER TABLE QSTN_LIST
 ADD TESTCASES MEDIUMTEXT;

ALTER TABLE QSTN_LIST
 ADD TIME_COMPLEXITY MEDIUMTEXT;

ALTER TABLE QSTN_LIST
 ADD EXPECTED_OUTPUT MEDIUMTEXT;

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atmpt_list`
--
ALTER TABLE `atmpt_list`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `exm_list`
--
ALTER TABLE `exm_list`
  MODIFY `exid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `exm_list` ADD ACCESSIBILITY TEXT;

ALTER TABLE `exm_list` ADD proctoring TEXT;

ALTER TABLE `exm_list` ADD status TEXT;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `qstn_list`
--
ALTER TABLE `qstn_list`
  MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
 
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;