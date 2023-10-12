<?php

namespace unit\frontend\controllers;

use app\entities\FileStorageItem;
use PHPUnit\Framework\TestCase;

class CabinetControllerTest extends TestCase
{
    /**
     * @dataProvider UploadFileProvider
     */
    public function testUploadFile($FILES, $POST, $userId, $hasDbError, $isUpload, $expected)
    {
        $errors = [];
        $secureDir = '';
        $isSecure = 0;

        if (empty($FILES)) {
            $errors[] = 'Файл не выбран!';
            return self::assertSame($expected, $errors);
        }

        if (!empty($FILES) && $FILES["filename"]["error"] != UPLOAD_ERR_OK) {
            $errors[] = 'Код ошибки при загрузке файла: ' . $FILES["filename"]["error"];
            return self::assertSame($expected, $errors);
        }

        if ($POST['secure'] == 1) {
            $secureDir = "secure/$userId/";
            if (!FileStorageItem::checkSecureDir($userId)) {
                $errors[] = 'Ошибка создания папки на диске! Путь к директории: ' . $secureDir;
                return self::assertSame($expected, $errors);
            }
            $isSecure = 1;
        }

        $baseUrl = FileStorageItem::UPLOADS_DIR . $secureDir;
        $fileName = $FILES["filename"]["name"];
        $parts = explode('.', $fileName);
        $extension = array_pop($parts);
        $fileName = implode($parts);
        $fileName = $fileName . '(2023-08-15 21:17:31)';
        $path = $baseUrl . $fileName . '.' . $extension;
        $createdAt = time();
        $expirationTime = $POST['expiration'] == 1 ? time() + 36000 : null;

        if (!$isUpload) {
            $errors[] = 'Ошибка загрузки на диск! Путь загрузки: ' . $path;
            return self::assertSame($expected, $errors);
        }

        if ($hasDbError) {
            $errors[] = 'Ошибка при вставке записи в БД!';
            return self::assertSame($expected, $errors);
        }

        $result = [
            'user_id' => $userId,
            'base_url' => $baseUrl,
            'path' => $path,
            'type' => $extension,
            'name' => $fileName,
            'created_at' => $createdAt,
            'is_secure' => $isSecure,
            'expiration_time' => $expirationTime
        ];

        self::assertSame($expected, $result);
    }

    public function UploadFileProvider(): array
    {
        return [
            [
                [],
                [],
                1,
                false,
                true,
                [
                    'Файл не выбран!'
                ]
            ],
            [
                [
                    "filename" =>
                        [
                            "name" => 'test.jpg',
                            "error" => 0,
                            "tmp_name" => 'tmp_test.jpg'
                        ]
                ],
                [
                    'secure' => 0,
                    'expiration' => 0
                ],
                1,
                false,
                true,
                [
                    'user_id' => 1,
                    'base_url' => FileStorageItem::UPLOADS_DIR,
                    'path' => FileStorageItem::UPLOADS_DIR . 'test(2023-08-15 21:17:31).jpg',
                    'type' => 'jpg',
                    'name' => 'test(2023-08-15 21:17:31)',
                    'created_at' => time(),
                    'is_secure' => 0,
                    'expiration_time' => null
                ]
            ]
        ];
    }

}