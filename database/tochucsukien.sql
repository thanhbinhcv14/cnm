-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 23, 2025 lúc 05:11 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tochucsukien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluan`
--

CREATE TABLE `binhluan` (
  `ID_BinhLuan` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Sukien` int(11) NOT NULL,
  `NoiDung` varchar(255) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_hotro`
--

CREATE TABLE `chat_hotro` (
  `ID_HoTro` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `NoiDungYeuCau` int(11) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `diadiem`
--

CREATE TABLE `diadiem` (
  `ID_DiaDiem` int(11) NOT NULL,
  `TenDiaDiem` varchar(255) NOT NULL,
  `DiaChi` varchar(255) NOT NULL,
  `TenChuSoHuu` varchar(255) NOT NULL,
  `SoDienThoai` varchar(10) NOT NULL,
  `Ngaytao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa`
--

CREATE TABLE `hoa` (
  `ID_HoaDon` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `NoiDungHoaDon` varchar(255) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `TrangThai` enum('Chưa thanh toán','Đã thanh toán') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanquyen`
--

CREATE TABLE `phanquyen` (
  `ID_Role` int(11) NOT NULL,
  `TenRole` varchar(255) NOT NULL,
  `GhiChu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phanquyen`
--

INSERT INTO `phanquyen` (`ID_Role`, `TenRole`, `GhiChu`) VALUES
(1, 'Quản trị viên', ''),
(2, 'Khách hàng', ''),
(3, 'Đơn vị tổ chức', ''),
(4, 'Nhân viên', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sukien`
--

CREATE TABLE `sukien` (
  `ID_Sukien` int(11) NOT NULL,
  `TenSuKien` varchar(255) NOT NULL,
  `HinhAnh` varchar(255) NOT NULL,
  `ThoiGianBatDau` date NOT NULL,
  `ThoiGianKetThuc` date NOT NULL,
  `NoiDung` varchar(255) NOT NULL,
  `TheLoai` varchar(255) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Ve` int(11) NOT NULL,
  `ID_DiaDiem` int(11) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `ID_ThanhToan` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `NoiDungThanhToan` varchar(255) NOT NULL,
  `PhuongThucThanhToan` varchar(255) NOT NULL,
  `Qrcode` varchar(255) NOT NULL,
  `NgayThanhToan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tichdiem`
--

CREATE TABLE `tichdiem` (
  `ID_TichDiem` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Ve` int(11) NOT NULL,
  `Diem` double NOT NULL,
  `NoiDung` varchar(255) NOT NULL,
  `Thoi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `ID_User` int(11) NOT NULL,
  `HoTen` varchar(255) NOT NULL,
  `SoDienThoai` varchar(10) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `TenDangNhap` varchar(255) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `ID_Role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`ID_User`, `HoTen`, `SoDienThoai`, `Email`, `TenDangNhap`, `MatKhau`, `ID_Role`) VALUES
(1, 'BuiThanhBinh', '0707102543', 'thanhbinh@gmail.com', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1),
(2, 'Ngyễn Văn A', '0914564565', 'nguyenvana@gmail.com', 'khachhang1', 'e10adc3949ba59abbe56e057f20f883e', 2),
(3, 'Nguyễn Văn B', '0914564512', 'vanB@gmail.com', 'khachhang2', 'e10adc3949ba59abbe56e057f20f883e', 2),
(4, 'Hồ Văn Cường', '0987845236', 'hovancuong@gmail.com', 'donvitochuc1', 'e10adc3949ba59abbe56e057f20f883e', 3),
(5, 'Hồ Văn B', '0914564512', 'hovanB@gmail.com', 'donvitochuc2', 'e10adc3949ba59abbe56e057f20f883e', 3),
(6, 'Nhân viên 1', '0914564512', 'nhanvien1@gmail.com', 'nhanvien1', 'e10adc3949ba59abbe56e057f20f883e', 4),
(7, 'Nhân viên 2', '0914564534', 'nhanvien2@gmail.com', 'nhanvien2', 'e10adc3949ba59abbe56e057f20f883e', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ve`
--

CREATE TABLE `ve` (
  `ID_Ve` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Sukien` int(11) NOT NULL,
  `HangVe` enum('Thường','Vip') NOT NULL,
  `GiaVe` decimal(10,0) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `TrangThai` enum('Còn vé','Hết vé') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`ID_BinhLuan`),
  ADD KEY `FK_binhluan_user` (`ID_User`),
  ADD KEY `FK_binhluan_sukien` (`ID_Sukien`);

--
-- Chỉ mục cho bảng `chat_hotro`
--
ALTER TABLE `chat_hotro`
  ADD PRIMARY KEY (`ID_HoTro`),
  ADD KEY `FK_hotro_user` (`ID_User`);

--
-- Chỉ mục cho bảng `diadiem`
--
ALTER TABLE `diadiem`
  ADD PRIMARY KEY (`ID_DiaDiem`);

--
-- Chỉ mục cho bảng `hoa`
--
ALTER TABLE `hoa`
  ADD PRIMARY KEY (`ID_HoaDon`);

--
-- Chỉ mục cho bảng `phanquyen`
--
ALTER TABLE `phanquyen`
  ADD PRIMARY KEY (`ID_Role`);

--
-- Chỉ mục cho bảng `sukien`
--
ALTER TABLE `sukien`
  ADD PRIMARY KEY (`ID_Sukien`),
  ADD KEY `FK_sukien_user` (`ID_User`),
  ADD KEY `FK_sukien_ve` (`ID_Ve`),
  ADD KEY `FK_sukien_diadiem` (`ID_DiaDiem`);

--
-- Chỉ mục cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`ID_ThanhToan`),
  ADD KEY `FK_thanhtoan_user` (`ID_User`);

--
-- Chỉ mục cho bảng `tichdiem`
--
ALTER TABLE `tichdiem`
  ADD PRIMARY KEY (`ID_TichDiem`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_User`),
  ADD KEY `FK_role_user` (`ID_Role`);

--
-- Chỉ mục cho bảng `ve`
--
ALTER TABLE `ve`
  ADD PRIMARY KEY (`ID_Ve`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `ID_BinhLuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chat_hotro`
--
ALTER TABLE `chat_hotro`
  MODIFY `ID_HoTro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `diadiem`
--
ALTER TABLE `diadiem`
  MODIFY `ID_DiaDiem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoa`
--
ALTER TABLE `hoa`
  MODIFY `ID_HoaDon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phanquyen`
--
ALTER TABLE `phanquyen`
  MODIFY `ID_Role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `sukien`
--
ALTER TABLE `sukien`
  MODIFY `ID_Sukien` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `ID_ThanhToan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tichdiem`
--
ALTER TABLE `tichdiem`
  MODIFY `ID_TichDiem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `ve`
--
ALTER TABLE `ve`
  MODIFY `ID_Ve` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD CONSTRAINT `FK_binhluan_sukien` FOREIGN KEY (`ID_Sukien`) REFERENCES `sukien` (`ID_Sukien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_binhluan_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chat_hotro`
--
ALTER TABLE `chat_hotro`
  ADD CONSTRAINT `FK_hotro_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `sukien`
--
ALTER TABLE `sukien`
  ADD CONSTRAINT `FK_sukien_diadiem` FOREIGN KEY (`ID_DiaDiem`) REFERENCES `diadiem` (`ID_DiaDiem`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sukien_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sukien_ve` FOREIGN KEY (`ID_Ve`) REFERENCES `ve` (`ID_Ve`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `FK_thanhtoan_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_role_user` FOREIGN KEY (`ID_Role`) REFERENCES `phanquyen` (`ID_Role`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
