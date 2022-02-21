<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//create the tables and seed some data
$app->get('/init/db',function ($request,$responce) {
$sql="-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Feb 21, 2022 at 07:07 AM
-- Server version: 5.7.34
-- PHP Version: 8.0.8

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+00:00';


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `aircrafts`
--

CREATE TABLE `aircrafts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aircrafts`
--

INSERT INTO `aircrafts` (`id`, `name`, `type`, `size`, `status`, `priority`) VALUES
(966, 'Air France', 'emergency', 'large', 'standby', '11'),
(967, 'delta', 'emergency', 'large', 'standby', '11'),
(968, 'emirates', 'passenger', 'small', 'standby', '32'),
(969, 'aircanda', 'vip', 'large', 'standby', '21'),
(970, 'Egypt air', 'cargo', 'small', 'standby', '42'),
(971, 'American Air lines', 'passenger', 'small', 'standby', '32'),
(972, 'Air France', 'passenger', 'large', 'standby', '31'),
(973, 'Delta', 'vip', 'small', 'standby', '22'),
(974, 'Aero Mexico', 'vip', 'small', 'standby', '22');

-- --------------------------------------------------------

--
-- Table structure for table `departures`
--

CREATE TABLE `departures` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aircrafts`
--
ALTER TABLE `aircrafts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departures`
--
ALTER TABLE `departures`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aircrafts`
--
ALTER TABLE `aircrafts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=975;

--
-- AUTO_INCREMENT for table `departures`
--
ALTER TABLE `departures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=973;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
";
    try {
        $db= new DB();
        $conn = $db->connect();
        $stmt= $conn->prepare($sql);
        $stmt->execute();
        $db=null;
        $responce->getBody()->write(json_encode('done!'));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(200);
    }
    catch (PDOException $e){
        $error=array(
            "message"=>$e->getMessage()
        );
        $responce->getBody()->write(json_encode($error));
        return $responce
        ->withHeader('content-type','application/json')
        ->withStatus(500);
    }
});