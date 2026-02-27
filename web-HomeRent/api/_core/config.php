<?php
declare(strict_types=1);

const APP_NAME = 'ايجار ويب';
const BASE_URL = 'https://aziz-server.myqnapcloud.com:8081/web-HomeRent'; // عدّل حسب مسارك

const DB_HOST = '127.0.0.1:3307';
const DB_NAME = 'web-homerent';
const DB_USER = 'web-user';
const DB_PASS = '664422$$';
const DB_CHARSET = 'utf8mb4';

const JWT_SECRET = 'CHANGE_ME_TO_LONG_RANDOM_SECRET';
const JWT_ISSUER = 'IjarWebAPI';
const JWT_TTL_SECONDS = 60 * 60 * 24 * 14;

const UPLOAD_ROOT = __DIR__ . '/../../storage/uploads';
const MAX_UPLOAD_BYTES = 10 * 1024 * 1024;
const ALLOWED_MIMES = ['image/jpeg','image/png','image/webp','application/pdf'];

const MAIL_FROM = 'no-reply@example.com';
const MAIL_FROM_NAME = 'ايجار ويب';


// Optional: Protect cron HTTP access (leave empty to disable token check)
const CRON_TOKEN = '';
