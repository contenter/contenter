﻿

/* Data for the `mail_templates` table */

INSERT INTO `mail_templates` (`template`, `subject`, `text`, `html`) VALUES
  ('user/forgot-password', 'Forgot password', '', '<p>Hello, User !\r\n<br /><br />\r\nYou asked to change your password click the link below or copy and paste it into your web browser:\r\n<br />\r\n{$BASE_PATH}confirmation/{$hash}/accept.html\r\n<br /><br />\r\nIf you never asked to change your password click the link below or copy and paste it into your web browser:\r\n{$BASE_PATH}confirmation/{$hash}/decline.html\r\n</p>'),
  ('user/new-password', 'New Password', '', '<p>Hello, User !\r\n<br /><br />\r\nYour new password:\r\n<br />\r\n{$password}\r\n<br />\r\nNow you can login clicking the link\r\n<br />\r\n<a href=\"{$BASE_PATH}user/login.html\">{$BASE_PATH}user/login.html</a>\r\n</p>');


