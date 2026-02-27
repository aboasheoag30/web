-- owner
-- php -r 'echo password_hash("YourPassword", PASSWORD_BCRYPT), PHP_EOL;'
INSERT INTO users (role, full_name, email, phone, password_hash)
VALUES ('owner', 'مالك العقار', 'owner@example.com', '0500000000', '$2y$10$REPLACE_ME_WITH_BCRYPT_HASH');
