-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 09, 2025 lúc 07:59 AM
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
  `HinhAnh` varchar(255) NOT NULL,
  `TenChuSoHuu` varchar(255) NOT NULL,
  `SoDienThoai` varchar(10) NOT NULL,
  `Ngaytao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `diadiem`
--

INSERT INTO `diadiem` (`ID_DiaDiem`, `TenDiaDiem`, `DiaChi`, `HinhAnh`, `TenChuSoHuu`, `SoDienThoai`, `Ngaytao`) VALUES
(1, 'Sân Vận Động Phú Thọ', '1 Lữ Gia, Quận 11, TP.HCM', '', 'Nguyên Văn A', '0901234567', '2025-04-25 01:00:00'),
(2, 'Nhà Văn Hóa Thanh Niên', '4A Phạm Ngọc Thạch, Quận 1, TP.HCM', '', 'Trần Thị B', '0912345678', '2025-04-25 01:10:00'),
(3, 'Sân Khấu Lan Anh', '291 Cách Mạng Tháng 8, Quận 10, TP.HCM', '', 'Lê Văn C', '0923456789', '2025-04-25 01:20:00'),
(4, 'Trung Tâm Hội Nghị GEM Center', '8 Nguyễn Bỉnh Khiêm, Quận 1, TP.HCM', '', 'Phạm Thị D', '0934567890', '2025-04-25 01:30:00'),
(5, 'Công Viên Lê Văn Tám', 'Đường Võ Thị Sáu, Quận 1, TP.HCM', '', 'Ngô Văn E', '0945678901', '2025-04-25 01:40:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

CREATE TABLE `giohang` (
  `ID_GioHang` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Ve` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `giohang`
--

INSERT INTO `giohang` (`ID_GioHang`, `ID_User`, `ID_Ve`, `SoLuong`, `NgayTao`) VALUES
(1, 8, 10, 1, '2025-05-03 13:57:31'),
(2, 1, 8, 1, '2025-05-05 07:56:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
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
  `ThoiGianBatDau` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ThoiGianKetThuc` timestamp NOT NULL DEFAULT current_timestamp(),
  `NoiDung` varchar(255) NOT NULL,
  `TheLoai` varchar(255) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Ve` int(11) NOT NULL,
  `ID_DiaDiem` int(11) NOT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `qrcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sukien`
--

INSERT INTO `sukien` (`ID_Sukien`, `TenSuKien`, `HinhAnh`, `ThoiGianBatDau`, `ThoiGianKetThuc`, `NoiDung`, `TheLoai`, `ID_User`, `ID_Ve`, `ID_DiaDiem`, `NgayTao`, `qrcode`) VALUES
(1, 'Lễ Hội Ánh Sáng', 'anhsang1.jpg', '2025-05-01 12:00:00', '2025-05-02 15:00:00', 'Lễ hội với hàng ngàn ánh đèn nghệ thuật.', 'Lễ hội', 4, 1, 1, '2025-04-24 21:13:21', ''),
(2, 'Đêm Nhạc Rock', 'rock1.jpg', '2025-06-10 13:00:00', '2025-06-10 17:00:00', 'Chương trình nhạc rock với nhiều ban nhạc nổi tiếng.', 'Âm nhạc', 4, 2, 2, '2025-04-24 21:14:24', ''),
(3, 'Chung Kết Rap Việt', 'rapviet1.jpg', '2025-07-20 09:00:00', '2025-07-20 18:00:00', 'Sự kiện hoành tráng với dàn sao rap hot nhất.', 'Truyền hình', 4, 3, 3, '2025-04-24 21:15:24', ''),
(4, 'Workshop Công Nghệ', 'workshop1.jpeg', '2025-08-15 01:00:00', '2025-08-16 07:00:00', 'Hội thảo chia sẻ về công nghệ tương lai.', 'Giáo dục', 5, 4, 4, '2025-04-24 21:16:20', ''),
(5, 'Lễ Hội Ẩm Thực', 'amthuc1.jpg', '2025-09-10 01:00:00', '2025-09-12 07:00:00', 'Thưởng thức các món ăn từ khắp nơi trên thế giới.', 'Ẩm thực', 5, 5, 5, '2025-04-24 21:17:16', '');

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
  `ID_Role` int(11) NOT NULL,
  `HinhAnh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`ID_User`, `HoTen`, `SoDienThoai`, `Email`, `TenDangNhap`, `MatKhau`, `ID_Role`, `HinhAnh`) VALUES
(1, 'BuiThanhBinh', '0707102543', 'thanhbinh@gmail.com', 'admin', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 1, ''),
(2, 'Ngyễn Văn A', '0914564565', 'nguyenvana@gmail.com', 'khachhang1', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 2, ''),
(3, 'Nguyễn Văn B', '0914564512', 'vanB@gmail.com', 'khachhang2', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 2, ''),
(4, 'Hồ Văn Cường', '0987845236', 'hovancuong@gmail.com', 'donvitochuc1', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 3, 'Hinh/avatars/user_4_1746690016.jpg'),
(5, 'Hồ Văn B', '0914564512', 'hovanB@gmail.com', 'donvitochuc2', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 3, ''),
(6, 'Nhân viên 1', '0914564512', 'nhanvien1@gmail.com', 'nhanvien1', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 4, ''),
(7, 'Nhân viên 2', '0914564534', 'nhanvien2@gmail.com', 'nhanvien2', '$2y$10$BiAedOFqCcOoTY07rMfLMuUWHVeVGTeDsU45BQDi.uE2s7CX.Bg9m', 4, ''),
(8, 'Bùi Thanh Bình', '0707102546', 'binhnekkk@gmail.com', 'binhnekkk123', '$2y$10$qBoQifkljhts5GBKuSRbDuFuvTXqKty08PkkY9vil22Xxi02WgT1i', 2, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ve`
--

CREATE TABLE `ve` (
  `ID_Ve` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `ID_Sukien` int(11) NOT NULL,
  `HangVe` varchar(255) NOT NULL,
  `GiaVe` decimal(10,0) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `TrangThai` enum('Còn vé','Hết vé') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ve`
--

INSERT INTO `ve` (`ID_Ve`, `ID_User`, `ID_Sukien`, `HangVe`, `GiaVe`, `SoLuong`, `TrangThai`) VALUES
(1, 4, 1, 'Thường', 100000, 100, 'Còn vé'),
(2, 4, 1, 'Vip', 200000, 50, 'Còn vé'),
(3, 4, 2, 'Thường', 300000, 200, 'Còn vé'),
(4, 4, 2, 'Vip', 500000, 100, 'Còn vé'),
(5, 4, 3, 'Thường', 800000, 200, 'Còn vé'),
(6, 4, 3, 'Vip', 1000000, 300, 'Còn vé'),
(7, 5, 4, 'Thường', 100000, 100, 'Còn vé'),
(8, 5, 4, 'Vip', 200000, 50, 'Còn vé'),
(9, 5, 5, 'Thường', 150000, 90, 'Còn vé'),
(10, 5, 5, 'Vip', 200000, 40, 'Còn vé');

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
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`ID_GioHang`),
  ADD KEY `ID_User` (`ID_User`),
  ADD KEY `ID_Ve` (`ID_Ve`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
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
  ADD PRIMARY KEY (`ID_Ve`),
  ADD KEY `FK_ve` (`ID_Sukien`);

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
  MODIFY `ID_DiaDiem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `giohang`
--
ALTER TABLE `giohang`
  MODIFY `ID_GioHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
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
  MODIFY `ID_Sukien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `ve`
--
ALTER TABLE `ve`
  MODIFY `ID_Ve` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `FK_giohang_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_giohang_ve` FOREIGN KEY (`ID_Ve`) REFERENCES `ve` (`ID_Ve`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `sukien`
--
ALTER TABLE `sukien`
  ADD CONSTRAINT `FK_sukien_diadiem` FOREIGN KEY (`ID_DiaDiem`) REFERENCES `diadiem` (`ID_DiaDiem`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sukien_user` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;

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
