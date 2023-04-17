-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 17, 2023 at 12:17 PM
-- Server version: 8.0.32-0ubuntu0.20.04.2
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `openbudget`
--

-- --------------------------------------------------------

--
-- Table structure for table `bots`
--

CREATE TABLE `bots` (
  `id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `board` varchar(255) DEFAULT NULL,
  `voice_limit` bigint NOT NULL DEFAULT '0',
  `voice_price` bigint NOT NULL,
  `ref_price` bigint NOT NULL,
  `min_payment` bigint NOT NULL DEFAULT '0',
  `ref_mode` int NOT NULL DEFAULT '0',
  `mandatory_subscription` int DEFAULT NULL,
  `mandatory_chatid` varchar(255) DEFAULT NULL,
  `mandatory_link` varchar(255) DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_messages`
--

CREATE TABLE `bot_messages` (
  `id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `key` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Table structure for table `bot_owners`
--

CREATE TABLE `bot_owners` (
  `id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `owner_id` bigint NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `owner_username` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `chat_id` bigint NOT NULL,
  `lastlogged` bigint NOT NULL,
  `bots` text NOT NULL,
  `status` int NOT NULL,
  `level` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `name`, `chat_id`, `lastlogged`, `bots`, `status`, `level`) VALUES
(1, 'Superadmin', 412358911, 1681724997, '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` bigint NOT NULL,
  `data` text,
  `bot_id` bigint NOT NULL,
  `chat_id` bigint NOT NULL,
  `amount` bigint NOT NULL DEFAULT '0',
  `time` bigint NOT NULL,
  `status` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `referals`
--

CREATE TABLE `referals` (
  `id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `chat_id` bigint NOT NULL,
  `owner_id` bigint NOT NULL,
  `time` bigint NOT NULL,
  `status` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(1, 'voting_button', '🗣 Ovoz berish'),
(2, 'balance_button', '💳 Hisobim'),
(3, 'referrer_button', '🔗 Referal'),
(4, 'help_button', '🆘 Yordam'),
(5, 'about_button', '🤖 Bot haqida'),
(6, 'resend_sms', '🔄 SMS kodni qayta yuborish'),
(7, 'enter_otp_code', '✉️ SMS orqali yuborilgan kodni kiriting...'),
(8, 'error_resending_otp', '⚠️ SMS kodni qayta yuborishda xatolik'),
(9, 'otp_has_ben_resent', '📲 SMS kod qayta yuborildi'),
(10, 'try_again_otp', '⏳ Yangi SMS kod yuborish uchun {seconds} soniyadan so\'ng qayta urining.'),
(11, 'start_message', ' Kerakli bo\'limni tanlang 👇'),
(12, 'voting_button_message', '<b>🗣 Ovoz berish uchun telefon raqamni yuboring.</b>\r\n\r\n🤳 Namuna: <em>901234567</em> yoki <em>+998901234567</em>'),
(13, 'balance_button_message', '💰 Sizda umumiy <b>{all_voices} dona</b> berilgan ovozlardan <b>{balance} so\'m</b> mablag\' mavjud\r\n-------------------\r\nℹ️ Balansingizdagi mablag\'ni yechib olish uchun quyidagi tugmani bosib so\'rov yuboring 👇'),
(14, 'get_cash', '🔄 Pul yechib olish'),
(15, 'share_referrer_link', '👉 Referal manzilni do\'stlar bilan ulashish'),
(16, 'share_referrer_link_message', 'telegram boti orqali ovoz berib pul ishlashga taklif qilaman!\r\n\r\nShunchaki ovoz bering va pul ishlang! Bu juda oson!\r\n\r\n💰⬇️⬇️⬇️\r\n\r\n{referrer_link}'),
(17, 'referrer_button_message', 'ℹ️ Referal manzil orqali do\'stlaringizni botga taklif qiling va \"pul\" ishlab toping. Har bir referal uchun {ref_payment} so\'mdan taqdim etiladi.\r\n\r\n👨‍👩‍👦 Referal orqali qo\'shilganlar: {ref} dona\r\n\r\nSizning referal manzilingiz 👇\r\n\r\n{referrer_link}'),
(18, 'help_button_message', 'ℹ️ Agar sizda botdan foydalanish masalalarida savollar yoki takliflar bo\'lsa quyidagi havolalar orqali yordam olishingiz mumkun.\r\n\r\n🆘 Yordam guruhi: @guruhingizga_ozgartiring\r\n\r\n🧑‍💻 Admin: @user_name'),
(19, 'about_button_message', '👨‍💻 Dasturchi: @xayronmiz\r\n👉 Telegram kanal: @obudjetuz'),
(20, 'humans_message', '⚠️ Kechirasiz, <b>humans</b> operatorlari so\'rovnomada ishtirok eta olmaydi!'),
(21, 'cancel_button', '❌ Bekor qilish'),
(22, 'understand_message', 'Kechirasiz men sizni tushuna olmadim 🤷‍♂️'),
(23, 'internal_error_message', '⚠️ Kechirasiz, tizimga ulanishda xatolik!'),
(24, 'number_previously_used_message', '⚠️ Kechirasiz, ushbu raqam avvalroq ovoz berish jarayonida foydalanilgan.'),
(25, 'enter_captcha_message', '✍️ Iltimos xavfsizlik uchun yuqoridagi misol javobini yuboring!'),
(26, 'wrong_captcha_message', '🚫 Captcha natijasi mos kelmadi yoki javob kiritish vaqti tugagan. Iltmos, keyinroq qaytadan urinib ko\'ring.'),
(27, 'captcha_number_required_message', '🚫 Misol javobi raqamlardan iborat bo\'lishi lozim!'),
(28, 'wrong_otp_entered_message', '⚠️ Tasdiqlash kodi xato kiritildi!'),
(29, 'expired_otp_message', '⚠️ Tasdiqlash kodi muddat o\'tgan. Iltimos, qaytadan urinib ko\'ring!'),
(30, 'otp_undefined_error_message', '⚠️ Ovoz berish jarayonida aniqlanmagan xatolik. Iltimos, qaytadan urinib ko\'ring!'),
(31, 'vote_success_message', '✅ Ovoz berish muvaffaqiyatli amalga oshirildi!'),
(32, 'otp_code_number_format_required', '🚫 Tasdiqlash kodi raqamlardan iborat bo\'lishi lozim!'),
(33, 'over_limits_message', 'ℹ️ Ovoz berish jarayoni vaqtinchalik to\'xtatilgan.'),
(34, 'enter_payment_details_message', '<b>Pul</b> yechib olish uchun iltimos <b>Telefon yoki Karta </b> raqamni kiriting 👇'),
(35, 'lack_balance_message', 'ℹ️ Minimal pul yechish miqdori: {min_payment} so\'m'),
(36, 'payment_request_success_message', '✅ Pul yechib olish uchun so\'rov muvaffaqiyatli yuborildi'),
(37, 'payment_waiting_message', '⏳ Kechirasiz sizda avvalroq yuborilgan so\'rov mavjud. Iltimos, jarayon yakunlanishini kuting.'),
(38, 'payment_request_length_message', '⚠️ To\'lov ma\'lumoti uchun telefon yoki karta raqam kiritilishi lozim!'),
(39, 'new_referrer_message', '✅ Sizda yangi referal mavjud'),
(40, 'subscribed_message', '🙋‍♂️ A\'zo bo\'ldim'),
(41, 'go_to_channel_message', '👉 Kanalga o\'tish'),
(42, 'not_subscribed_alert_message', '⚠️ Kechirasiz, siz kanalga hali a\'zo bo\'lmagansiz.'),
(43, 'subscribe_message', 'ℹ️ Iltimos, botimizdan to\'laqonli foydalanish uchun avvalo kanalga a\'zo bo\'ling.'),
(44, 'bot_token', ''),
(45, 'vote_not_accepted_message', '⚠️ Ovoz qabul qilinmadi!'),
(46, 'channel_id', ''),
(47, 'channel_link', ''),
(48, 'channel_username', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `chat_id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `registered` bigint NOT NULL,
  `lastaction` bigint NOT NULL,
  `lastcommand` varchar(255) NOT NULL,
  `balance` bigint NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Table structure for table `voices`
--

CREATE TABLE `voices` (
  `id` bigint NOT NULL,
  `phone` varchar(52) DEFAULT NULL,
  `bot_id` bigint DEFAULT NULL,
  `chat_id` bigint DEFAULT NULL,
  `board` varchar(255) DEFAULT NULL,
  `time` bigint DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voice_process`
--

CREATE TABLE `voice_process` (
  `id` bigint NOT NULL,
  `captcha_file` varchar(255) NOT NULL,
  `captcha_key` varchar(255) NOT NULL,
  `captcha_result` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `chat_id` bigint NOT NULL,
  `bot_id` bigint NOT NULL,
  `otpKey` varchar(255) DEFAULT NULL,
  `retryAfter` int DEFAULT NULL,
  `retry_begin` bigint DEFAULT NULL,
  `step` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bots`
--
ALTER TABLE `bots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot_messages`
--
ALTER TABLE `bot_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot_owners`
--
ALTER TABLE `bot_owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referals`
--
ALTER TABLE `referals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voices`
--
ALTER TABLE `voices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voice_process`
--
ALTER TABLE `voice_process`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bots`
--
ALTER TABLE `bots`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `bot_messages`
--
ALTER TABLE `bot_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `bot_owners`
--
ALTER TABLE `bot_owners`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `referals`
--
ALTER TABLE `referals`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `voices`
--
ALTER TABLE `voices`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `voice_process`
--
ALTER TABLE `voice_process`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
