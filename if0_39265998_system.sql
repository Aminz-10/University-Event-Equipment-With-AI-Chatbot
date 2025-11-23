-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.byetcluster.com
-- Generation Time: Nov 21, 2025 at 11:04 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39265998_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `staff_num` int(10) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_password` varchar(50) NOT NULL,
  `staff_tel` varchar(50) NOT NULL,
  `staff_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`staff_num`, `staff_name`, `staff_password`, `staff_tel`, `staff_email`) VALUES
(123457, 'Ahmad Zaki', 'zaki123', '01334655274', 'ahmad.zaki@example.com'),
(123458, 'Noraini Yusuf', 'noraini456', '0178765432', 'noraini.yusuf@example.com'),
(123459, 'Faizal Rahman', 'faizal789', '0111122334', 'faizal.rahman@example.com'),
(123460, 'Suraya Mahmud', 'suraya321', '0168899001', 'suraya.mahmud@example.com'),
(123461, 'Liyana Musa', 'liyana654', '0189988776', 'liyana.musa@example.com'),
(123462, 'Daniel Chong', 'daniel987', '0193344556', 'daniel.chong@example.com'),
(123463, 'Zul Hadi', 'zulpass', '0129988776', 'zul.hadi@example.com'),
(123464, 'Azlin Hashim', 'azlinpass', '0135566778', 'azlin.hashim@example.com'),
(123465, 'Salmah Idris', 'salmahpass', '0147766554', 'salmah.idris@example.com'),
(123466, 'Hafiz Othman', 'hafizpass', '0156677889', 'hafiz.othman@example.com'),
(123477, 'Amirul Amin', '12345', '0194857374', 'amin@gmail.com'),
(12345678, 'Idham Zuhri', '123', '0122412221', 'idham04@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(10) NOT NULL,
  `stud_num` int(10) NOT NULL,
  `staff_num` int(10) DEFAULT NULL,
  `event_name` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `club_name` varchar(50) NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `stud_num`, `staff_num`, `event_name`, `status`, `start_date`, `end_date`, `club_name`, `return_date`) VALUES
(114, 2023226262, 12345678, 'DINNER BASCO', 'returned', '2025-06-25', '2025-06-26', 'BACHELOR OF COMPUTER SCIENCE SOCIETY (BASCO)', '2025-06-25'),
(115, 2023226262, NULL, 'MEET AND GREET', 'rejected', '2025-06-26', '2025-06-27', 'ALPHA ', NULL),
(116, 2023226262, 123457, 'Hackathon', 'returned', '2025-06-26', '2025-06-27', 'BACHELOR OF COMPUTER SCIENCE SOCIETY (BASCO)', '2025-06-25'),
(117, 2023451002, 123458, 'Mental Health Day', 'returned', '2025-06-27', '2025-06-27', 'BADAN PEER KAUNSELOR (PEERS)', '2025-06-25'),
(118, 2023226261, 123459, 'Scientific Forum	', 'returned', '2025-06-28', '2025-06-29', 'APPLIED SCIENCE TAPAH (ASET)', '2025-06-25'),
(119, 2023261622, 123460, 'Web Dev Workshop', 'returned', '2025-06-30', '2025-06-30', ' DIPLOMA SAINS KOMPUTER (DISK)', '2025-06-25'),
(120, 2023451004, 123461, 'Literature Fest', 'returned', '2025-07-01', '2025-07-02', 'SISWA SISWI SAINS MATEMATIK (PERSIK)', '2025-06-25'),
(121, 2023451014, 123462, 'Listening Skills', 'returned', '2025-07-02', '2025-07-02', 'BADAN PEER KAUNSELOR (PEERS)', '2025-06-25'),
(122, 2023451002, 123463, 'Excel Workshop', 'rejected', '2025-07-04', '2025-07-04', 'SISWA SISWI STATISTIK (PESISTA)', NULL),
(123, 2023451003, 123457, 'Stargazing Briefing', 'returned', '2025-07-06', '2025-07-06', 'AL-BIRUNI ASTRONOMY CLUB (AAC)', '2025-06-25'),
(124, 2023451016, NULL, 'Accounting Day', 'rejected', '2025-07-08', '2025-07-08', 'DIPLOMA PERAKAUNAN SISTEM MAKLUMAT (PERDAIS)', NULL),
(125, 2023443918, 123457, 'BASCO Alumni Meet', 'returned', '2025-07-10', '2025-07-10', 'BACHELOR OF COMPUTER SCIENCE SOCIETY (BASCO)', '2025-06-25'),
(126, 2023451017, 123465, 'U-FASI Briefing', 'returned', '2025-07-11', '2025-07-11', 'FASILITATOR (U-FASI)', '2025-06-25'),
(127, 2023451007, 123466, 'Wellness Wksp', 'returned', '2025-07-14', '2025-07-14', 'PROGRAM SISWA SIHAT (PROSIS)', '2025-06-25'),
(128, 2023451019, 123466, 'Conflict Handling', 'returned', '2025-07-15', '2025-07-15', 'BADAN PEER KAUNSELOR (PEERS)', '2025-06-25'),
(129, 2023261622, 123466, 'OOP Crash Course', 'returned', '2025-07-17', '2025-07-18', ' DIPLOMA SAINS KOMPUTER (DISK)', '2025-06-25'),
(130, 2023451003, 123457, 'Mars Day', 'returned', '2025-07-19', '2025-07-20', 'AL-BIRUNI ASTRONOMY CLUB (AAC)', '2025-06-25'),
(131, 2023451010, NULL, 'Team Building', 'rejected', '2025-08-01', '2025-08-01', 'BADAN PEER KAUNSELOR (PEERS)', NULL),
(132, 2023451006, 123460, 'ALPHA Motivational Talk', 'returned', '2025-08-03', '2025-08-03', 'ALPHA ', '2025-06-25'),
(133, 2023443918, 123462, 'Final Year Meetup', 'returned', '2025-08-05', '2025-08-06', 'BACHELOR OF COMPUTER SCIENCE SOCIETY (BASCO)', '2025-06-25'),
(134, 2023451012, 123463, 'Art Performance', 'returned', '2025-08-08', '2025-08-08', 'BADAN KESENIAN DAN KEBUDAYAAN (DANSENI)', '2025-06-25'),
(135, 2023451005, 123464, ' Appreciation Night', 'returned', '2025-08-10', '2025-08-10', 'BADAN PEER KAUNSELOR (PEERS)', '2025-06-25'),
(136, 2023226262, 123457, 'SILAT', 'returned', '2025-06-25', '2025-06-26', 'SENI SILAT CEKAK MALAYSIA (PSSCM)', '2025-07-13'),
(137, 2033, NULL, 'test', 'rejected', '2025-06-27', '2025-06-27', 'TAPAH DEBATE CLUB (TDC)', NULL),
(138, 289078654, 123457, 'uitm', 'returned', '2025-06-27', '2025-06-27', 'NON-RESIDENCES', '2025-07-13'),
(139, 2023226262, 123457, 'SILAT CEKAK SHOW', 'returned', '2025-07-14', '2025-07-15', 'SENI SILAT LINCAH MALAYSIA (PSSLM)', '2025-07-24'),
(140, 2023226262, NULL, 'MOVIE NIGHT', 'pending', '2025-07-14', '2025-07-14', 'BETA', NULL),
(141, 2023226262, 123457, 'MOVIE NIGHT 2', 'borrowed', '2025-07-14', '2025-07-14', 'TAPAH BADMINTON CLUB (TBC)', NULL),
(142, 2023261622, 123457, 'm', 'borrowed', '2025-07-24', '2025-07-24', 'TAPAH DEBATE CLUB (TDC)', NULL),
(143, 2023261622, 123457, 'gfdu', 'borrowed', '2025-07-24', '2025-07-24', 'TAPAH BADMINTON CLUB (TBC)', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipment`
--

CREATE TABLE `booking_equipment` (
  `id_equipment` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_equipment`
--

INSERT INTO `booking_equipment` (`id_equipment`, `id_booking`, `qty`) VALUES
(42, 114, 1),
(42, 115, 1),
(42, 116, 1),
(42, 119, 1),
(42, 122, 1),
(42, 124, 1),
(42, 129, 1),
(42, 132, 1),
(42, 136, 1),
(46, 118, 50),
(46, 127, 30),
(46, 135, 20),
(48, 140, 5),
(48, 141, 5),
(57, 139, 4),
(60, 117, 4),
(60, 124, 3),
(60, 129, 4),
(60, 131, 2),
(66, 119, 2),
(66, 130, 2),
(69, 122, 1),
(69, 125, 1),
(69, 132, 1),
(76, 116, 5),
(76, 118, 4),
(76, 120, 4),
(76, 123, 2),
(76, 125, 3),
(76, 131, 3),
(76, 133, 4),
(78, 128, 1),
(78, 143, 1),
(79, 121, 1),
(80, 126, 50),
(81, 117, 3),
(81, 120, 2),
(81, 121, 2),
(81, 123, 1),
(81, 126, 3),
(81, 127, 2),
(81, 130, 2),
(81, 133, 3),
(81, 135, 3),
(83, 128, 2),
(83, 134, 2),
(83, 138, 2),
(84, 134, 2),
(84, 137, 1),
(84, 142, 1);

-- --------------------------------------------------------

--
-- Table structure for table `club`
--

CREATE TABLE `club` (
  `club_name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `adv_name` varchar(50) NOT NULL,
  `adv_tel` varchar(50) NOT NULL,
  `adv_email` varchar(50) NOT NULL,
  `adv_num` varchar(50) NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club`
--

INSERT INTO `club` (`club_name`, `type`, `adv_name`, `adv_tel`, `adv_email`, `adv_num`, `status`) VALUES
(' DIPLOMA SAINS KOMPUTER (DISK)', 'Close', 'Pn. Lily', '0136103842', 'lily564@gmail.com', 'A35', 'Active'),
('AL-BIRUNI ASTRONOMY CLUB (AAC)', 'Open', 'En. Afuan', '0107461953', 'afuan100@gmail.com', 'A94', 'active'),
('ALPHA ', 'Close', 'Pn. Amira', '0123580659', 'amira123@gmail.com', 'A15', 'Active'),
('APPLIED SCIENCE TAPAH (ASET)', 'Close', 'Ms. Azura', '0185129467', 'azura78@gmail.com', 'A90', 'Active'),
('BACHELOR OF COMPUTER SCIENCE SOCIETY (BASCO)', 'Close', 'Pn. Itaza Mohtar', '018-9988776', 'liyana.musa@example.com', 'A020', 'Active'),
('BADAN KESENIAN DAN KEBUDAYAAN (DANSENI)', 'Open', 'Ms. Fatimah', '0149256083', 'fatimah349@gmail.com', 'A87', 'Active'),
('BADAN PEER KAUNSELOR (PEERS)', 'Open', 'En. Azdi', '0127365408', 'azdi125@gmail.com', 'A56', 'Active'),
('BETA', 'Close', 'En. Zul', '0103859172', 'zul@gmail.com', 'A69', 'active'),
('BRIGED SUKARELAWAN MAHASISWA (BRISUMA)', 'Open', 'En. Amirul ', '0107325849', 'amirul56@gmail.com', 'A23', 'Active'),
('DIPLOMA PERAKAUNAN SISTEM MAKLUMAT (PERDAIS)', 'Close', 'Pn. Siti', '0164298053', 'siti656@gmail.com', 'A76', 'Active'),
('FASILITATOR (U-FASI)', 'Open', 'Pn. Asma', '0138504917', 'asma900@gmail.com', 'A33', 'Active'),
('GAMMA', 'Close', 'En. Rizal', '01172958340', 'rizal90@gmail.com', 'A49', 'active'),
('GERAKAN PENGGUNA SISWA (GPS)', 'Open', 'En. Alif', '0197846501', 'alif567@gmail.com', 'A45', 'Active'),
('IKATAN MAHASISWA DINAMIK (IMAD)', 'Open', 'En. Zaki', '0104957281', 'zaki1@gmail.com', 'A23', 'Active'),
('JAWATANKUASA SUKAN PELAJAR (JSP)', 'Open', 'En. Mika', '0124616283', 'megat123@gmail.com', 'A98', 'Disband'),
('KELANASISWA MALAYSIA (KSM)', 'Open', 'En. Shahril', '0189512763', 'shahril888@gmail.com', 'A70', 'active'),
('NON-RESIDENCES', 'Close', 'En. Syed', '0126394875', 'syed001@gmail.com', 'A91', 'active'),
('PERSATUAN TAEKWONDO TAPAH (PTT)', 'Open', 'Ms. Alia', '01130498276', 'alia8900@gmail.com', 'A65', 'active'),
('PERTAHANAN AWAM (PERSPA)', 'Open', 'En. Haziq', '0194837025', 'haziqqq@gmail.com', 'A34', 'active'),
('PROGRAM SISWA SIHAT (PROSIS)', 'Open', 'Pn. Insyirah', '0176921358', 'insyirah354@gmail.com', 'A36', 'Active'),
('RAKAN SISWA YADIM (RSY)', 'Open', 'En. Afif', '01139512108', 'afif455@gmail.com', 'A67', 'Active'),
('SEKRETARIAT RUKUN NEGARA (SRN)', 'Open', 'Pn. Samsiah', '0173208496', 'samsiah@gmail.com', 'A12', 'active'),
('SENI SILAT CEKAK MALAYSIA (PSSCM)', 'Open', 'En. Jamal', '0125783492', 'jamal@gmail.com', 'A40', 'active'),
('SENI SILAT LINCAH MALAYSIA (PSSLM)', 'Open', 'En. Mahdi', '0136908247', 'mahdi@gmail.com', 'A81', 'Active'),
('SISWA SISWI DIPLOMA PERAKAUNAN (PERSIDA)', 'Close', 'Pn. Iman', '0173672984', 'iman67@gmail.com', 'A77', 'Active'),
('SISWA SISWI SAINS MATEMATIK (PERSIK)', 'Close', 'Ms. Sam', '0126598114', 'sam345@gmail.com', 'A58', 'active'),
('SISWA SISWI STATISTIK (PESISTA)', 'Close', 'Pn. Mimi', '0148539721', 'mimi@gmail.com', 'A09', 'Active'),
('SUKSIS', 'Close', 'Tn. Syidi', '01125847910', 'syidi45@gmail.com', 'A90', 'Disband'),
('TAPAH BADMINTON CLUB (TBC)', 'Open', 'En. Wafi', '0182034733', 'wafi123@gmail.com', 'A66', 'active'),
('TAPAH DEBATE CLUB (TDC)', 'Open', 'Ms. Syaza', '0163847592', 'syazaaa2@gmail.com', 'A75', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id_equipment` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `model` varchar(50) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id_equipment`, `name`, `category`, `status`, `qty`, `model`, `picture`) VALUES
(42, 'LCD Projector', 'Visual Equipment', 'Available', 0, 'Epson EB-X41', 'uploads/1750611268_projector.jpg'),
(46, 'Banquet Chair', 'Furniture & Seating', 'Available', 100, NULL, 'uploads/1750611916_banquet chair.jpeg'),
(47, 'Folding Table', 'Furniture & Seating', 'Maintenance', 20, NULL, 'uploads/1750611972_folding table.jpeg'),
(48, '10x10 Canopy', 'Tents & Canopies', 'Available', 5, NULL, 'uploads/1750647137_X7-10x10-Blue.webp'),
(56, 'Bunting Stand', 'Signage & Display', 'Available', 6, NULL, 'uploads/1750647387_t-stand-768x829.png'),
(57, 'Roll-Up Banner Stand', 'Signage & Display', 'Available', 4, NULL, 'uploads/1750647478_Luxury_Roll_Up_Stand_1724677648928.png'),
(58, 'Chafing Dish', 'Catering Equipment', 'Available', 10, NULL, 'uploads/1750612743_dish.png'),
(59, 'Beverage Dispenser', 'Catering Equipment', 'Available', 6, NULL, 'uploads/1750647637_s-l1200.jpg'),
(60, 'Standing Fan', 'Climate Control', 'Available', 12, NULL, 'uploads/1750611374_fan.jpeg'),
(66, 'Equipment Trolley', 'Transportation & Storage', 'Available', 4, NULL, 'uploads/1750612681_trolley.png'),
(69, 'Speaker 1.0', 'Audio Equipment', 'Available', 1, 'Havit SQ133BT 4', 'uploads/1750612439_havit.jpg'),
(73, 'Speaker 3.0', 'Audio Equipment', 'Maintenance', 2, 'MIPRO XL', 'uploads/1750612425_mipro.jpg'),
(74, 'Portable Aircond', 'Climate Control', 'Available', 1, 'DAIKIN', 'uploads/1750612568_porrable, aircond.png'),
(76, 'Round Table', 'Furniture & Seating', 'Available', 10, NULL, 'uploads/1750648032_Lorell-Banquet-Folding-Table-5b3b5938-810f-49ee-87d4-5f54ad01071d_911905664_big.webp'),
(78, 'Rostrum', 'Stage Equipment', 'Available', 2, NULL, 'uploads/eqp_6858c65de49316.82377521.jpg'),
(79, 'Coffee Table', 'Furniture & Seating', 'Available', 2, NULL, 'uploads/eqp_6858c716abb3a3.16918709.jpg'),
(80, 'Plastic Chair', 'Furniture & Seating', 'Available', 100, NULL, 'uploads/eqp_68593046ef1759.52738448.jpg'),
(81, 'Sofa', 'Furniture & Seating', 'Available', 10, NULL, 'uploads/eqp_685931a49aec84.85509127.jpg'),
(82, 'Mini Stage 4\' x 4\'', 'Stage Equipment', 'Available', 20, NULL, 'uploads/eqp_685932251568b7.97805411.png'),
(83, 'Wired Microphone ', 'Audio Equipment', 'Available', 1, ' Shure SM58', 'uploads/eqp_685932a03870c0.96299537.jpeg'),
(84, 'Microphone Stand', 'Audio Equipment', 'Available', 2, NULL, 'uploads/eqp_685933596aadd9.87567624.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `stud_num` int(10) NOT NULL,
  `stud_name` varchar(100) NOT NULL,
  `stud_pass` varchar(50) NOT NULL,
  `stud_tel` varchar(50) NOT NULL,
  `stud_email` varchar(50) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `faculty` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`stud_num`, `stud_name`, `stud_pass`, `stud_tel`, `stud_email`, `course_code`, `faculty`) VALUES
(0, '2023226262', 'q', 'q', 'wafi@gmail.com', 'CS110', 'FSKM'),
(123, 'hdjs', '123', '018393', 'jsksksksj@hshsh.mxndn', 'CS110', 'FSKM'),
(2033, 'aidil', 'aidil', '111', 'aidil@gmail.com', 'AC120', 'FP'),
(2222, 'siti', '123', '4214', 'siti@gmail.com', 'AS120', 'FSG'),
(2038473, 'mira', 'mira', '01284748', 'mira@gmail.com', 'AS120', 'FSG'),
(289078654, 'ahmad', '1234', '017654890', 'prosy-mirror0u@icloud.com', 'CS230', 'FSKM'),
(2022800956, 'AMIRUL AMIN', 'pass123', '0174436191', 'amin@gmail.com', 'CDCS230', 'FSKM'),
(2023226261, 'MUHAMMAD IDHAM BIN ZUHRI', '123', '0182034733', 'wafiaffandi2004@gmail.com', 'AS120', 'FSG'),
(2023226262, 'MUHAMAD WAFI BIN AFFANDI', '123', '0182034733', 'wafiaffandi2004@gmail.com', 'CS230', 'FSKM'),
(2023261622, 'MUHAMMAD DANISH IRFAN BIN MUHAIZUL', '123456', '0124616283', 'irfandanish@gmail.com', 'CS110', 'FSKM'),
(2023443915, 'mal', '123', '0109748450', 'akmal@45', 'AS120', 'FSG'),
(2023443917, 'Akmals', '123', '0109748460', 'akmal@123', 'CS230', 'FSKM'),
(2023443918, 'AKMAL WAFI', '12345', '0124440944', 'akmalwafi182@gmail.com', 'CS230', 'FSKM'),
(2023451001, 'AHMAD HAKIM BIN ROSLAN	', 'ahk123', '0172213345', 'ahmadhakim01@gmail.com', 'CS110', 'FSKM'),
(2023451002, 'NUR IZZATI BINTI ZULKIFLI	', 'nizz123', '0134227689', 'nurizzati.zul@gmail.com', 'CS111', 'FSKM'),
(2023451003, 'MUHAMMAD AIMAN BIN ZAINAL	', 'aiman456', '0198734265', 'aimanz@gmail.com', 'CS112', 'FSKM'),
(2023451004, 'SITI NURFARAHIN BINTI AZMI', 'siti321', '0147236890', 'farah.azmi@gmail.com', 'CS143', 'FSKM'),
(2023451005, 'DANIAL HAFIZ BIN RAZAK', 'danial123', '0178923654', 'danial.razak@gmail.com', 'CS230', 'FSKM'),
(2023451006, 'AMIRUL HAKIM BIN SHAHRUL', 'pass321', '0128347659', 'amirulhakim@gmail.com', 'CS110', 'FSKM'),
(2023451007, 'AINA SOFIA BINTI KAMARUDDIN', 'aina456', '0139427890', 'ainasofia.k@gmail.com', 'CS111', 'FSKM'),
(2023451008, 'HAZIQ FAHMI BIN AZLAN	', 'haziq123', '0187364298', 'haziqfahmi.az@gmail.com', 'CS112', 'FSKM'),
(2023451009, 'NURUL AQILAH BINTI HARIS	', 'aqilah789', '0119023764', 'aqilahharis.nur@gmail.com', 'CS143', 'FSKM'),
(2023451010, 'SYAZWAN BIN RAHIM	', 'syaz123', '0145637890', 'syazwanr@gmail.com', 'CS230', 'FSKM'),
(2023451011, 'MUHAMMAD LUQMAN BIN HAFIZ	', 'luq321', '0128763490', 'luqman.hafiz@gmail.com', 'AS120', 'FSG'),
(2023451012, 'AISYAH HANIS BINTI FAIZ	', 'aisyah456', '0168743298', 'aisyah.fz@gmail.com', 'AS120', 'FSG'),
(2023451013, 'MUHAMMAD IRFAN BIN AZHARI	', 'irfan234	', '0187639821', 'irfan.azhari@gmail.com', 'AS120', 'FSG'),
(2023451014, 'FARAH ILYANA BINTI NAZMI	', 'farah789', '0178493021', 'ilyana.farah@gmail.com', 'AS120', 'FSG'),
(2023451015, 'NABIL HAKIM BIN KHALID', 'nabil123', '0147768290', 'nabilhakim.k@gmail.com', 'AC120', 'FP'),
(2023451016, 'BALQIS SOFIAH BINTI RAZALI	', 'balqis321', '0167389201', 'balqissofiah.rz@gmail.com', 'AC110', 'FP'),
(2023451017, 'HAFIZAH BINTI HAMDAN	', 'hafizah12', '0198734652', 'hafizah.hamdan@gmail.com', 'AC120', 'FP'),
(2023451018, 'MUHAMMAD RAZIN BIN SALLEH	', 'razin789', '0128734298', 'mrazin.salleh@gmail.com', 'AC110', 'FP'),
(2023451019, 'AMALINA BINTI AZHAR	', 'amalina90', '0138472319', 'amalina.azhar90@gmail.com', 'AC120', 'FP'),
(2023451020, 'HAZWAN NAZMI BIN SHAHIR', 'hazwan001', '0187346582', 'hazwannz.shahir@gmail.com', 'AC110', 'FP'),
(2147483647, 'rahmat ali', '1234', '0163846281', 'abdulrahmat@gmail.com', 'CS230', 'FSKM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`staff_num`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `stud_num` (`stud_num`),
  ADD KEY `fk_club_name` (`club_name`),
  ADD KEY `fk_staff_num` (`staff_num`);

--
-- Indexes for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD PRIMARY KEY (`id_equipment`,`id_booking`),
  ADD KEY `fk_booking` (`id_booking`);

--
-- Indexes for table `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`club_name`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id_equipment`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`stud_num`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id_equipment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`stud_num`) REFERENCES `user` (`stud_num`),
  ADD CONSTRAINT `fk_club_name` FOREIGN KEY (`club_name`) REFERENCES `club` (`club_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_staff_num` FOREIGN KEY (`staff_num`) REFERENCES `admin` (`staff_num`);

--
-- Constraints for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD CONSTRAINT `fk_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`),
  ADD CONSTRAINT `fk_equipment` FOREIGN KEY (`id_equipment`) REFERENCES `equipment` (`id_equipment`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
