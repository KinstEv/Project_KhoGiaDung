-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th4 10, 2026 lúc 08:37 AM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `khohanggiadung`
--

DELIMITER $$
--
-- Thủ tục
--
DROP PROCEDURE IF EXISTS `PhanBoKhoHangMoi`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PhanBoKhoHangMoi` ()   BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE j INT DEFAULT 1;

    -- DÃY A: Hàng lớn, nặng (300x80x200)
    SET i = 1;
    WHILE i <= 2 DO 
        SET j = 1;
        WHILE j <= 5 DO 
            INSERT INTO `vitri` (`maViTri`, `day`, `ke`, `o`, `daiToiDa`, `rongToiDa`, `caoToiDa`, `maDanhMucUuTien`, `choPhepXepChong`) 
            VALUES (CONCAT('A', i, '-', j), 'A', i, j, 300, 80, 200, 'DM01', FALSE); -- DM01: Tủ lạnh
            SET j = j + 1;
        END WHILE;
        SET i = i + 1;
    END WHILE;

    -- DÃY B: Hàng lớn vừa (130x120x100)
    SET i = 1;
    WHILE i <= 3 DO 
        SET j = 1;
        WHILE j <= 8 DO 
            INSERT INTO `vitri` (`maViTri`, `day`, `ke`, `o`, `daiToiDa`, `rongToiDa`, `caoToiDa`, `maDanhMucUuTien`, `choPhepXepChong`) 
            VALUES (CONCAT('B', i, '-', j), 'B', i, j, 130, 120, 100, 'DM03', FALSE); -- DM03: Quạt lớn/TV
            SET j = j + 1;
        END WHILE;
        SET i = i + 1;
    END WHILE;

    -- DÃY C: Hàng nhỏ vừa - Cho xếp chồng
    SET i = 1;
    WHILE i <= 5 DO 
        SET j = 1;
        WHILE j <= 15 DO 
            INSERT INTO `vitri` (`maViTri`, `day`, `ke`, `o`, `daiToiDa`, `rongToiDa`, `caoToiDa`, `maDanhMucUuTien`, `choPhepXepChong`) 
            VALUES (CONCAT('C', i, '-', j), 'C', i, j, 100, 60, 100, 'DM02', TRUE); -- DM02: Nồi cơm/Lò vi sóng
            SET j = j + 1;
        END WHILE;
        SET i = i + 1;
    END WHILE;

    -- DÃY D: Hàng nhỏ - Cho xếp chồng
    SET i = 1;
    WHILE i <= 5 DO 
        SET j = 1;
        WHILE j <= 20 DO 
            INSERT INTO `vitri` (`maViTri`, `day`, `ke`, `o`, `daiToiDa`, `rongToiDa`, `caoToiDa`, `maDanhMucUuTien`, `choPhepXepChong`) 
            VALUES (CONCAT('D', i, '-', j), 'D', i, j, 60, 50, 50, NULL, TRUE);
            SET j = j + 1;
        END WHILE;
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_phieudathang`
--

DROP TABLE IF EXISTS `ct_phieudathang`;
CREATE TABLE IF NOT EXISTS `ct_phieudathang` (
  `maDH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuong` int NOT NULL,
  `soLuongDaNhap` int DEFAULT '0',
  `donGia` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`maDH`,`maHH`),
  KEY `fk_ctpdh_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_phieunhap`
--

DROP TABLE IF EXISTS `ct_phieunhap`;
CREATE TABLE IF NOT EXISTS `ct_phieunhap` (
  `maPN` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuong` int NOT NULL,
  `donGia` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`maPN`,`maHH`),
  KEY `fk_ctpn_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_phieuxuat`
--

DROP TABLE IF EXISTS `ct_phieuxuat`;
CREATE TABLE IF NOT EXISTS `ct_phieuxuat` (
  `maPX` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuong` int NOT NULL,
  `donGia` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`maPX`,`maHH`),
  KEY `fk_ctpx_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_phieuxuat_lo`
--

DROP TABLE IF EXISTS `ct_phieuxuat_lo`;
CREATE TABLE IF NOT EXISTS `ct_phieuxuat_lo` (
  `maPX` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maLo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maViTri` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuong` int NOT NULL,
  PRIMARY KEY (`maPX`,`maHH`,`maLo`,`maViTri`),
  KEY `fk_ctpx_lo_lo` (`maLo`),
  KEY `fk_ctpx_lo_vt` (`maViTri`),
  KEY `fk_ctpx_lo_px` (`maPX`),
  KEY `fk_ctpx_lo_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_phieuxuat_serial`
--

DROP TABLE IF EXISTS `ct_phieuxuat_serial`;
CREATE TABLE IF NOT EXISTS `ct_phieuxuat_serial` (
  `maPX` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maLo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maViTri` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maPX`,`maHH`,`serial`),
  KEY `fk_ctpx_ser_px` (`maPX`),
  KEY `fk_ctpx_ser_hh` (`maHH`),
  KEY `fk_ctpx_ser_serial` (`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

DROP TABLE IF EXISTS `danhmuc`;
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `maDanhMuc` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenDanhMuc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maDanhMucCha` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maDanhMuc`),
  KEY `fk_danhmuc_parent` (`maDanhMucCha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`maDanhMuc`, `tenDanhMuc`, `maDanhMucCha`) VALUES
('BIG_DGD', 'Điện gia dụng', NULL),
('BIG_DOGD', 'Đồ bếp gia dụng', NULL),
('BIG_DTDM', 'Điện tử, điện máy', NULL),
('BIG_DTVT', 'Điện tử, viễn thông', NULL),
('BIG_PK', 'Phụ kiện', NULL),
('SUB_BALO', 'Balo, túi chống sốc', 'BIG_PK'),
('SUB_BDN', 'Bình đựng nước', 'BIG_DOGD'),
('SUB_BDST', 'Bình đun siêu tốc', 'BIG_DGD'),
('SUB_BG', 'Bếp ga', 'BIG_DGD'),
('SUB_BHN', 'Bếp hồng ngoại', 'BIG_DGD'),
('SUB_BLGN', 'Bình, ly giữ nhiệt', 'BIG_DOGD'),
('SUB_BLN', 'Bộ lau nhà', 'BIG_DOGD'),
('SUB_BP', 'Bàn phím', 'BIG_PK'),
('SUB_BT', 'Bếp từ', 'BIG_DGD'),
('SUB_BTD', 'Bình thủy điện', 'BIG_DGD'),
('SUB_BU', 'Bàn ủi', 'BIG_DGD'),
('SUB_CAM', 'Camera giám sát', 'BIG_PK'),
('SUB_CHAO', 'Chảo', 'BIG_DOGD'),
('SUB_CHUOT', 'Chuột máy tính', 'BIG_PK'),
('SUB_CLN', 'Cốc lọc nước đầu nguồn', 'BIG_DGD'),
('SUB_CNNL', 'Cây nước nóng lạnh', 'BIG_DGD'),
('SUB_CS', 'Cáp sạc', 'BIG_PK'),
('SUB_DAO', 'Dao', 'BIG_DOGD'),
('SUB_DCMD', 'Dụng cụ mài dao', 'BIG_DOGD'),
('SUB_DKTV', 'Điều khiển Tivi', 'BIG_PK'),
('SUB_DT', 'Điện thoại', 'BIG_DTVT'),
('SUB_DTTM', 'Đồng hồ thông minh', 'BIG_DTVT'),
('SUB_GD', 'Giá đỡ máy giặt, máy lọc nước, chân loa', 'BIG_PK'),
('SUB_HDDD', 'Hộp đựng đồ đa năng', 'BIG_DOGD'),
('SUB_HDMI', 'Cáp HDMI, cáp tivi', 'BIG_PK'),
('SUB_HDTP', 'Hộp đựng thực phẩm', 'BIG_DOGD'),
('SUB_KEO', 'Kéo', 'BIG_DOGD'),
('SUB_KTV', 'Khung treo Tivi', 'BIG_PK'),
('SUB_LD', 'Lẩu điện', 'BIG_DGD'),
('SUB_LMH', 'Máy làm sữa hạt', 'BIG_DGD'),
('SUB_LMLN', 'Lõi máy lọc nước', 'BIG_DGD'),
('SUB_LN', 'Lò nướng', 'BIG_DGD'),
('SUB_LOA', 'Loa', 'BIG_DTDM'),
('SUB_LT', 'Laptop', 'BIG_DTVT'),
('SUB_LVS', 'Lò vi sóng', 'BIG_DGD'),
('SUB_MCG', 'Máy chơi game', 'BIG_DTVT'),
('SUB_MD', 'Miếng dán', 'BIG_PK'),
('SUB_MDC', 'Miếng dán Camera', 'BIG_PK'),
('SUB_METC', 'Máy ép trái cây', 'BIG_DGD'),
('SUB_MG', 'Máy giặt', 'BIG_DTDM'),
('SUB_MHA', 'Máy hút ẩm', 'BIG_DGD'),
('SUB_MHB', 'Máy hút bụi', 'BIG_DGD'),
('SUB_MHM', 'Máy hút mùi', 'BIG_DGD'),
('SUB_MHMH', 'Màn hình máy tính', 'BIG_DTVT'),
('SUB_MI', 'Máy in', 'BIG_DTVT'),
('SUB_MIC', 'Micro', 'BIG_PK'),
('SUB_MKR', 'Micro karaoke', 'BIG_DTDM'),
('SUB_ML', 'Máy lạnh', 'BIG_DTDM'),
('SUB_MLKK', 'Máy lọc không khí', 'BIG_DGD'),
('SUB_MLN', 'Máy lọc nước', 'BIG_DGD'),
('SUB_MNB', 'Máy nhồi bột, đánh trứng', 'BIG_DGD'),
('SUB_MNN', 'Máy nước nóng', 'BIG_DTDM'),
('SUB_MPCF', 'Máy pha cà phê', 'BIG_DGD'),
('SUB_MSQA', 'Máy sấy quần áo', 'BIG_DTDM'),
('SUB_MST', 'Máy sấy tóc', 'BIG_DGD'),
('SUB_MTB', 'Máy tính bảng', 'BIG_DTVT'),
('SUB_MUC', 'Mực In', 'BIG_DTVT'),
('SUB_MVC', 'Máy vắt cam', 'BIG_DGD'),
('SUB_MXST', 'Máy xay sinh tố', 'BIG_DGD'),
('SUB_MXT', 'Máy xay thịt', 'BIG_DGD'),
('SUB_NAS', 'Nồi áp suất', 'BIG_DGD'),
('SUB_NCD', 'Nồi cơm điện', 'BIG_DGD'),
('SUB_NCKD', 'Nồi chiên không dầu', 'BIG_DGD'),
('SUB_NOI', 'Nồi', 'BIG_DOGD'),
('SUB_OCDD', 'Ổ cứng di động', 'BIG_PK'),
('SUB_OTB', 'Ốp lưng tablet', 'BIG_PK'),
('SUB_OTL', 'Ốp lưng đ.thoại', 'BIG_PK'),
('SUB_PC', 'Máy tính để bàn', 'BIG_DTVT'),
('SUB_PK_LOA', 'Loa (Phụ kiện)', 'BIG_PK'),
('SUB_PKTB', 'Phụ kiện tablet', 'BIG_PK'),
('SUB_PM', 'Phần mềm', 'BIG_PK'),
('SUB_QDH', 'Quạt điều hoà', 'BIG_DGD'),
('SUB_QS', 'Quạt sưởi', 'BIG_DGD'),
('SUB_QUAT', 'Quạt', 'BIG_DGD'),
('SUB_RBHB', 'Robot hút bụi', 'BIG_DGD'),
('SUB_SDP', 'Sạc dự phòng', 'BIG_PK'),
('SUB_TBDV', 'Thiết bị định vị, phụ kiện', 'BIG_PK'),
('SUB_TBM', 'Thiết thiết bị mạng', 'BIG_PK'),
('SUB_TDTM', 'Tủ đông, tủ mát', 'BIG_DTDM'),
('SUB_THOT', 'Thớt', 'BIG_DOGD'),
('SUB_TL', 'Tủ lạnh', 'BIG_DTDM'),
('SUB_TN', 'Tai nghe', 'BIG_PK'),
('SUB_TNHO', 'Thẻ nhớ', 'BIG_PK'),
('SUB_TV', 'Tivi', 'BIG_DTDM'),
('SUB_USB', 'USB', 'BIG_PK'),
('SUB_VM', 'Vợt muỗi', 'BIG_DOGD');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donvitinh`
--

DROP TABLE IF EXISTS `donvitinh`;
CREATE TABLE IF NOT EXISTS `donvitinh` (
  `maDVT` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenDVT` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`maDVT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donvitinh`
--

INSERT INTO `donvitinh` (`maDVT`, `tenDVT`) VALUES
('DVT01', 'Cái'),
('DVT02', 'Chiếc'),
('DVT03', 'Bộ'),
('DVT04', 'Hộp'),
('DVT05', 'Thùng'),
('DVT06', 'Cặp'),
('DVT07', 'Cuộn'),
('DVT08', 'Mét'),
('DVT09', 'Vỉ'),
('DVT10', 'Lốc'),
('DVT11', 'Túi'),
('DVT12', 'Bao');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hanghoa`
--

DROP TABLE IF EXISTS `hanghoa`;
CREATE TABLE IF NOT EXISTS `hanghoa` (
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenHH` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maDVT` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thuongHieu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moTa` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maDanhMuc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loaiHang` enum('LO','SERIAL') COLLATE utf8mb4_unicode_ci DEFAULT 'LO',
  `chieuDai` int DEFAULT '0',
  `chieuRong` int DEFAULT '0',
  `chieuCao` int DEFAULT '0',
  `quyTacXoay` enum('CO_DINH','XOAY_NGANG','TU_DO') COLLATE utf8mb4_unicode_ci DEFAULT 'XOAY_NGANG' COMMENT 'CO_DINH: Không xoay, XOAY_NGANG: Chỉ hoán đổi Dài/Rộng, TU_DO: Xoay 6 hướng',
  `thoiGianBaoHanh` int DEFAULT '12' COMMENT 'Thời gian bảo hành cho khách (Tháng)',
  PRIMARY KEY (`maHH`),
  KEY `fk_hanghoa_dvt` (`maDVT`),
  KEY `fk_hanghoa_danhmuc` (`maDanhMuc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hanghoa_serial`
--

DROP TABLE IF EXISTS `hanghoa_serial`;
CREATE TABLE IF NOT EXISTS `hanghoa_serial` (
  `serial` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maLo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangThai` int DEFAULT '1',
  `maViTri` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`serial`),
  KEY `fk_hhs_lo` (`maLo`),
  KEY `fk_hhs_vitri` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE IF NOT EXISTS `khachhang` (
  `maKH` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenKH` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diaChi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangThai` tinyint DEFAULT '1',
  PRIMARY KEY (`maKH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`maKH`, `tenKH`, `diaChi`, `sdt`, `email`, `trangThai`) VALUES
('KH00002', '1', '1', '1', '123@gmail.com', 1),
('KH00003', 'Nguyễn Đình Trí', 'nhà bè', '0123456789', '123@gmail.com', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lohang`
--

DROP TABLE IF EXISTS `lohang`;
CREATE TABLE IF NOT EXISTS `lohang` (
  `maLo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maPN` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuongNhap` int NOT NULL,
  `ngayNhap` date NOT NULL,
  `hanBaoHanh` date DEFAULT NULL,
  PRIMARY KEY (`maLo`),
  KEY `fk_lo_pn` (`maPN`),
  KEY `fk_lo_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lo_hang_vi_tri`
--

DROP TABLE IF EXISTS `lo_hang_vi_tri`;
CREATE TABLE IF NOT EXISTS `lo_hang_vi_tri` (
  `maLVT` int NOT NULL AUTO_INCREMENT,
  `maLo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maViTri` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soLuong` int NOT NULL,
  PRIMARY KEY (`maLVT`),
  KEY `fk_lvt_lo` (`maLo`),
  KEY `fk_lvt_vitri` (`maViTri`)
) ENGINE=InnoDB AUTO_INCREMENT=769 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

DROP TABLE IF EXISTS `nguoidung`;
CREATE TABLE IF NOT EXISTS `nguoidung` (
  `maND` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenND` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taiKhoan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matKhau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maVaiTro` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hoatDong` tinyint(1) DEFAULT '1' COMMENT '1: Đang hoạt động, 0: Bị khóa',
  PRIMARY KEY (`maND`),
  UNIQUE KEY `taiKhoan` (`taiKhoan`),
  KEY `fk_nguoidung_vaitro` (`maVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`maND`, `tenND`, `email`, `sdt`, `taiKhoan`, `matKhau`, `maVaiTro`, `hoatDong`) VALUES
('ND01', 'Admin', 'admin@gmail.com', '0987654321', 'admin', '$2y$10$OFuUSY8zdBRIZ5LO1fTglOnanDX8UxCWT5Vko5EEkALk/VakvTyq6', 'VT_ADMIN', 1),
('ND02', 'Tô Nhật Hào', 'nhathao0910@gmail.com', '0123456789', 'nhathao0910', '$2y$10$OFuUSY8zdBRIZ5LO1fTglOnanDX8UxCWT5Vko5EEkALk/VakvTyq6', 'VT_KHO', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

DROP TABLE IF EXISTS `nhacungcap`;
CREATE TABLE IF NOT EXISTS `nhacungcap` (
  `maNCC` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenNCC` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diaChi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangThai` tinyint DEFAULT '1',
  PRIMARY KEY (`maNCC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`maNCC`, `tenNCC`, `diaChi`, `sdt`, `email`, `trangThai`) VALUES
('NCC002', 'Tô Nhật Hào', '123', '0813956301', '123@gmail.com', 0),
('NCC003', 'Công ty ABC', '180 cao lỗ p. chánh hưng tp.hcm', '0813956301', 'abc@a.com', 1),
('NCC004', 'Tô Nhật Hào', '1111', '0813956301', '123@gmail.com', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieubh`
--

DROP TABLE IF EXISTS `phieubh`;
CREATE TABLE IF NOT EXISTS `phieubh` (
  `maBH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maHH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngayNhan` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngayTra` datetime DEFAULT NULL,
  `moTaLoi` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangThai` int DEFAULT '0',
  `maND` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maBH`),
  KEY `fk_bh_serial` (`serial`),
  KEY `fk_bh_nd` (`maND`),
  KEY `fk_bh_hh` (`maHH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieudathang`
--

DROP TABLE IF EXISTS `phieudathang`;
CREATE TABLE IF NOT EXISTS `phieudathang` (
  `maDH` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngayDatHang` datetime DEFAULT CURRENT_TIMESTAMP,
  `maNCC` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangThai` int DEFAULT '0',
  `maND` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maDH`),
  KEY `fk_pdh_ncc` (`maNCC`),
  KEY `fk_pdh_nd` (`maND`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

DROP TABLE IF EXISTS `phieunhap`;
CREATE TABLE IF NOT EXISTS `phieunhap` (
  `maPN` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngayNhap` datetime DEFAULT CURRENT_TIMESTAMP,
  `maNCC` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghiChu` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maND` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maPN`),
  KEY `fk_pn_ncc` (`maNCC`),
  KEY `fk_pn_nd` (`maND`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuxuat`
--

DROP TABLE IF EXISTS `phieuxuat`;
CREATE TABLE IF NOT EXISTS `phieuxuat` (
  `maPX` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngayXuat` datetime DEFAULT CURRENT_TIMESTAMP,
  `maKH` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghiChu` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maNDXuat` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maPX`),
  KEY `fk_px_kh` (`maKH`),
  KEY `fk_px_nd` (`maNDXuat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quyen`
--

DROP TABLE IF EXISTS `quyen`;
CREATE TABLE IF NOT EXISTS `quyen` (
  `maQuyen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenQuyen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moTa` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maQuyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quyen`
--

INSERT INTO `quyen` (`maQuyen`, `tenQuyen`, `moTa`) VALUES
('Q_BAOCAO', 'Xem báo cáo', 'Quyền xem báo cáo tồn kho, doanh thu'),
('Q_BAOHANH', 'Quản lý bảo hành', 'Quyền tiếp nhận, tra cứu và xử lý đổi trả bảo hành'),
('Q_HETHONG', 'Quản trị hệ thống', 'Quyền quản lý người dùng và phân quyền'),
('Q_NHAP_KHO', 'Quản lý nhập kho', 'Quyền tạo và duyệt phiếu nhập'),
('Q_QL_HANG', 'Quản lý hàng hóa', 'Quyền thêm, sửa, xóa hàng hóa'),
('Q_XEM_HANG', 'Xem hàng hóa', 'Quyền xem danh sách và chi tiết hàng hóa'),
('Q_XUAT_KHO', 'Quản lý xuất kho', 'Quyền tạo và duyệt phiếu xuất');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quyen_vaitro`
--

DROP TABLE IF EXISTS `quyen_vaitro`;
CREATE TABLE IF NOT EXISTS `quyen_vaitro` (
  `maVaiTro` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maQuyen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`maVaiTro`,`maQuyen`),
  KEY `fk_qvt_quyen` (`maQuyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quyen_vaitro`
--

INSERT INTO `quyen_vaitro` (`maVaiTro`, `maQuyen`) VALUES
('VT_ADMIN', 'Q_BAOCAO'),
('VT_ADMIN', 'Q_BAOHANH'),
('VT_KHO', 'Q_BAOHANH'),
('VT_ADMIN', 'Q_HETHONG'),
('VT_ADMIN', 'Q_NHAP_KHO'),
('VT_KHO', 'Q_NHAP_KHO'),
('VT_ADMIN', 'Q_QL_HANG'),
('VT_ADMIN', 'Q_XEM_HANG'),
('VT_KHO', 'Q_XEM_HANG'),
('VT_ADMIN', 'Q_XUAT_KHO'),
('VT_KHO', 'Q_XUAT_KHO');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

DROP TABLE IF EXISTS `vaitro`;
CREATE TABLE IF NOT EXISTS `vaitro` (
  `maVaiTro` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenVaiTro` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moTa` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`maVaiTro`, `tenVaiTro`, `moTa`) VALUES
('VT_ADMIN', 'Quản trị viên', 'Có toàn quyền truy cập hệ thống'),
('VT_KHO', 'Nhân viên kho', 'Chỉ có quyền nhập xuất và xem hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vitri`
--

DROP TABLE IF EXISTS `vitri`;
CREATE TABLE IF NOT EXISTS `vitri` (
  `maViTri` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ke` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `daiToiDa` int DEFAULT '0',
  `rongToiDa` int DEFAULT '0',
  `caoToiDa` int DEFAULT '0',
  `maDanhMucUuTien` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangThai` enum('TRONG','DAY') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'TRONG',
  `choPhepXepChong` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`maViTri`),
  KEY `fk_vitri_danhmuc` (`maDanhMucUuTien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vitri`
--

INSERT INTO `vitri` (`maViTri`, `day`, `ke`, `o`, `daiToiDa`, `rongToiDa`, `caoToiDa`, `maDanhMucUuTien`, `trangThai`, `choPhepXepChong`) VALUES
('A1-1', 'A', '1', '1', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A1-2', 'A', '1', '2', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A1-3', 'A', '1', '3', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A1-4', 'A', '1', '4', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A1-5', 'A', '1', '5', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A2-1', 'A', '2', '1', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A2-2', 'A', '2', '2', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A2-3', 'A', '2', '3', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A2-4', 'A', '2', '4', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('A2-5', 'A', '2', '5', 300, 100, 200, 'BIG_DTDM', 'TRONG', 0),
('B1-1', 'B', '1', '1', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-2', 'B', '1', '2', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-3', 'B', '1', '3', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-4', 'B', '1', '4', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-5', 'B', '1', '5', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-6', 'B', '1', '6', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-7', 'B', '1', '7', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B1-8', 'B', '1', '8', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-1', 'B', '2', '1', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-2', 'B', '2', '2', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-3', 'B', '2', '3', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-4', 'B', '2', '4', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-5', 'B', '2', '5', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-6', 'B', '2', '6', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-7', 'B', '2', '7', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B2-8', 'B', '2', '8', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-1', 'B', '3', '1', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-2', 'B', '3', '2', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-3', 'B', '3', '3', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-4', 'B', '3', '4', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-5', 'B', '3', '5', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-6', 'B', '3', '6', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-7', 'B', '3', '7', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('B3-8', 'B', '3', '8', 150, 130, 120, 'BIG_DTDM', 'TRONG', 0),
('C1-1', 'C', '1', '1', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-10', 'C', '1', '10', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-11', 'C', '1', '11', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-12', 'C', '1', '12', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-2', 'C', '1', '2', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-3', 'C', '1', '3', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-4', 'C', '1', '4', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-5', 'C', '1', '5', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-6', 'C', '1', '6', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-7', 'C', '1', '7', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-8', 'C', '1', '8', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C1-9', 'C', '1', '9', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-1', 'C', '2', '1', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-10', 'C', '2', '10', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-11', 'C', '2', '11', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-12', 'C', '2', '12', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-2', 'C', '2', '2', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-3', 'C', '2', '3', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-4', 'C', '2', '4', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-5', 'C', '2', '5', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-6', 'C', '2', '6', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-7', 'C', '2', '7', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-8', 'C', '2', '8', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C2-9', 'C', '2', '9', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-1', 'C', '3', '1', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-10', 'C', '3', '10', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-11', 'C', '3', '11', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-12', 'C', '3', '12', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-2', 'C', '3', '2', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-3', 'C', '3', '3', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-4', 'C', '3', '4', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-5', 'C', '3', '5', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-6', 'C', '3', '6', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-7', 'C', '3', '7', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-8', 'C', '3', '8', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C3-9', 'C', '3', '9', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-1', 'C', '4', '1', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-10', 'C', '4', '10', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-11', 'C', '4', '11', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-12', 'C', '4', '12', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-2', 'C', '4', '2', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-3', 'C', '4', '3', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-4', 'C', '4', '4', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-5', 'C', '4', '5', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-6', 'C', '4', '6', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-7', 'C', '4', '7', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-8', 'C', '4', '8', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C4-9', 'C', '4', '9', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-1', 'C', '5', '1', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-10', 'C', '5', '10', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-11', 'C', '5', '11', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-12', 'C', '5', '12', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-2', 'C', '5', '2', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-3', 'C', '5', '3', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-4', 'C', '5', '4', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-5', 'C', '5', '5', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-6', 'C', '5', '6', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-7', 'C', '5', '7', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-8', 'C', '5', '8', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('C5-9', 'C', '5', '9', 120, 80, 100, 'BIG_DGD', 'TRONG', 1),
('D1-1', 'D', '1', '1', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-10', 'D', '1', '10', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-11', 'D', '1', '11', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-12', 'D', '1', '12', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-13', 'D', '1', '13', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-14', 'D', '1', '14', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-15', 'D', '1', '15', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-16', 'D', '1', '16', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-17', 'D', '1', '17', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-18', 'D', '1', '18', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-19', 'D', '1', '19', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-2', 'D', '1', '2', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-20', 'D', '1', '20', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-3', 'D', '1', '3', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-4', 'D', '1', '4', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-5', 'D', '1', '5', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-6', 'D', '1', '6', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-7', 'D', '1', '7', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-8', 'D', '1', '8', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D1-9', 'D', '1', '9', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-1', 'D', '2', '1', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-10', 'D', '2', '10', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-11', 'D', '2', '11', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-12', 'D', '2', '12', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-13', 'D', '2', '13', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-14', 'D', '2', '14', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-15', 'D', '2', '15', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-16', 'D', '2', '16', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-17', 'D', '2', '17', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-18', 'D', '2', '18', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-19', 'D', '2', '19', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-2', 'D', '2', '2', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-20', 'D', '2', '20', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-3', 'D', '2', '3', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-4', 'D', '2', '4', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-5', 'D', '2', '5', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-6', 'D', '2', '6', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-7', 'D', '2', '7', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-8', 'D', '2', '8', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D2-9', 'D', '2', '9', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-1', 'D', '3', '1', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-10', 'D', '3', '10', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-11', 'D', '3', '11', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-12', 'D', '3', '12', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-13', 'D', '3', '13', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-14', 'D', '3', '14', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-15', 'D', '3', '15', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-16', 'D', '3', '16', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-17', 'D', '3', '17', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-18', 'D', '3', '18', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-19', 'D', '3', '19', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-2', 'D', '3', '2', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-20', 'D', '3', '20', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-3', 'D', '3', '3', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-4', 'D', '3', '4', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-5', 'D', '3', '5', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-6', 'D', '3', '6', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-7', 'D', '3', '7', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-8', 'D', '3', '8', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D3-9', 'D', '3', '9', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-1', 'D', '4', '1', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-10', 'D', '4', '10', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-11', 'D', '4', '11', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-12', 'D', '4', '12', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-13', 'D', '4', '13', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-14', 'D', '4', '14', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-15', 'D', '4', '15', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-16', 'D', '4', '16', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-17', 'D', '4', '17', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-18', 'D', '4', '18', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-19', 'D', '4', '19', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-2', 'D', '4', '2', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-20', 'D', '4', '20', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-3', 'D', '4', '3', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-4', 'D', '4', '4', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-5', 'D', '4', '5', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-6', 'D', '4', '6', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-7', 'D', '4', '7', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-8', 'D', '4', '8', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D4-9', 'D', '4', '9', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-1', 'D', '5', '1', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-10', 'D', '5', '10', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-11', 'D', '5', '11', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-12', 'D', '5', '12', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-13', 'D', '5', '13', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-14', 'D', '5', '14', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-15', 'D', '5', '15', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-16', 'D', '5', '16', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-17', 'D', '5', '17', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-18', 'D', '5', '18', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-19', 'D', '5', '19', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-2', 'D', '5', '2', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-20', 'D', '5', '20', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-3', 'D', '5', '3', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-4', 'D', '5', '4', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-5', 'D', '5', '5', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-6', 'D', '5', '6', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-7', 'D', '5', '7', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-8', 'D', '5', '8', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1),
('D5-9', 'D', '5', '9', 80, 60, 60, 'BIG_DOGD', 'TRONG', 1);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ct_phieudathang`
--
ALTER TABLE `ct_phieudathang`
  ADD CONSTRAINT `fk_ctpdh_dh` FOREIGN KEY (`maDH`) REFERENCES `phieudathang` (`maDH`),
  ADD CONSTRAINT `fk_ctpdh_hh` FOREIGN KEY (`maHH`) REFERENCES `hanghoa` (`maHH`);

--
-- Các ràng buộc cho bảng `ct_phieunhap`
--
ALTER TABLE `ct_phieunhap`
  ADD CONSTRAINT `fk_ctpn_hh` FOREIGN KEY (`maHH`) REFERENCES `hanghoa` (`maHH`),
  ADD CONSTRAINT `fk_ctpn_pn` FOREIGN KEY (`maPN`) REFERENCES `phieunhap` (`maPN`);

--
-- Các ràng buộc cho bảng `ct_phieuxuat`
--
ALTER TABLE `ct_phieuxuat`
  ADD CONSTRAINT `fk_ctpx_hh` FOREIGN KEY (`maHH`) REFERENCES `hanghoa` (`maHH`),
  ADD CONSTRAINT `fk_ctpx_px` FOREIGN KEY (`maPX`) REFERENCES `phieuxuat` (`maPX`);

--
-- Các ràng buộc cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD CONSTRAINT `fk_danhmuc_parent` FOREIGN KEY (`maDanhMucCha`) REFERENCES `danhmuc` (`maDanhMuc`);

--
-- Các ràng buộc cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD CONSTRAINT `fk_hanghoa_danhmuc` FOREIGN KEY (`maDanhMuc`) REFERENCES `danhmuc` (`maDanhMuc`),
  ADD CONSTRAINT `fk_hanghoa_dvt` FOREIGN KEY (`maDVT`) REFERENCES `donvitinh` (`maDVT`);

--
-- Các ràng buộc cho bảng `hanghoa_serial`
--
ALTER TABLE `hanghoa_serial`
  ADD CONSTRAINT `fk_hhs_lo` FOREIGN KEY (`maLo`) REFERENCES `lohang` (`maLo`),
  ADD CONSTRAINT `fk_hhs_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitri` (`maViTri`);

--
-- Các ràng buộc cho bảng `lohang`
--
ALTER TABLE `lohang`
  ADD CONSTRAINT `fk_lo_hh` FOREIGN KEY (`maHH`) REFERENCES `hanghoa` (`maHH`),
  ADD CONSTRAINT `fk_lo_pn` FOREIGN KEY (`maPN`) REFERENCES `phieunhap` (`maPN`);

--
-- Các ràng buộc cho bảng `lo_hang_vi_tri`
--
ALTER TABLE `lo_hang_vi_tri`
  ADD CONSTRAINT `fk_lvt_lo` FOREIGN KEY (`maLo`) REFERENCES `lohang` (`maLo`),
  ADD CONSTRAINT `fk_lvt_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitri` (`maViTri`);

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `fk_nguoidung_vaitro` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`);

--
-- Các ràng buộc cho bảng `phieubh`
--
ALTER TABLE `phieubh`
  ADD CONSTRAINT `fk_bh_hh` FOREIGN KEY (`maHH`) REFERENCES `hanghoa` (`maHH`),
  ADD CONSTRAINT `fk_bh_nd` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `phieudathang`
--
ALTER TABLE `phieudathang`
  ADD CONSTRAINT `fk_pdh_ncc` FOREIGN KEY (`maNCC`) REFERENCES `nhacungcap` (`maNCC`),
  ADD CONSTRAINT `fk_pdh_nd` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `fk_pn_ncc` FOREIGN KEY (`maNCC`) REFERENCES `nhacungcap` (`maNCC`),
  ADD CONSTRAINT `fk_pn_nd` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD CONSTRAINT `fk_px_kh` FOREIGN KEY (`maKH`) REFERENCES `khachhang` (`maKH`),
  ADD CONSTRAINT `fk_px_nd` FOREIGN KEY (`maNDXuat`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `quyen_vaitro`
--
ALTER TABLE `quyen_vaitro`
  ADD CONSTRAINT `fk_qvt_quyen` FOREIGN KEY (`maQuyen`) REFERENCES `quyen` (`maQuyen`),
  ADD CONSTRAINT `fk_qvt_vaitro` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`);

--
-- Các ràng buộc cho bảng `vitri`
--
ALTER TABLE `vitri`
  ADD CONSTRAINT `fk_vitri_danhmuc` FOREIGN KEY (`maDanhMucUuTien`) REFERENCES `danhmuc` (`maDanhMuc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
