<?php

namespace Uploady;

use Farisc0de\PhpFileUploading\Upload;

class API
{
    public function __construct(
        private Upload $gateway,
        private Role $role,
        private Localization $localization,
        private \Farisc0de\PhpFileUploading\Utility $utils,
        private \Uploady\DataCollection $dataCollection,
        private \Wolfcast\BrowserDetection $browser,
        private \Uploady\Handler\UploadHandler $handler
    ) {
    }

    public function processRequest(string $method, ?string $id): void
    {
        switch ($method) {
            case "GET":
                $lang = $this->localization->loadLangauge("en");

                $file = $this->handler->getFile($id);

                if ($file == null) {
                    $this->responedNotFound($id);
                    exit();
                }

                echo $this->responedCreated($file);
                break;
            case 'POST':
                $lang = $this->localization->loadLangauge("en");

                $this->gateway->setSiteUrl(SITE_URL);

                $this->gateway->generateUserID();

                $this->gateway->createUserCloud("../" . UPLOAD_FOLDER);

                $this->gateway->setUploadFolder([
                    "folder_name" => $this->gateway->getUserCloud(UPLOAD_FOLDER),
                    "folder_path" => realpath($this->gateway->getUserCloud("../" . UPLOAD_FOLDER)),
                ]);

                $this->gateway->enableProtection();

                $this->gateway->setSizeLimit($this->role->get($_SESSION['user_role'])->size_limit);

                $this->gateway->generateFileID();

                $this->gateway->setUpload(new \Farisc0de\PhpFileUploading\File($_FILES['file'], $this->utils));

                if (!$this->gateway->checkIfNotEmpty()) {
                    http_response_code(400);
                    echo json_encode([
                        "error" => $lang["general"]['file_is_empty'],
                    ]);
                    exit();
                }

                $this->gateway->hashName();

                if (!$this->gateway->checkSize()) {
                    http_response_code(400);
                    echo json_encode([
                        "error" => $lang["general"]['file_is_too_large'],
                    ]);
                    exit();
                }

                if (
                    !$this->gateway->checkForbidden()
                ) {
                    http_response_code(400);
                    echo json_encode([
                        "error" => $lang["general"]['file_name_is_forbidden'],
                    ]);
                    exit();
                }

                if (
                    !$this->gateway->checkExtension()
                ) {
                    http_response_code(400);
                    echo json_encode([
                        "error" => $lang["general"]['file_type_is_not_allowed'],
                    ]);
                    exit();
                }

                if (
                    !$this->gateway->checkMime()
                ) {
                    http_response_code(400);
                    echo json_encode([
                        "error" => $lang["general"]['file_mime_type_is_not_allowed'],
                    ]);
                    exit();
                }

                if ($this->gateway->upload()) {
                    $this->handler->addFile(
                        $this->gateway->getFileID(),
                        $this->gateway->getUserID(),
                        $this->gateway->getJSON(),
                        json_encode(
                            [
                                "ip_address" => $this->dataCollection->collectIP(),
                                "country" => $this->dataCollection->idendifyCountry(),
                                "browser" => $this->dataCollection->getBrowser($this->browser),
                                "os" => $this->dataCollection->getOS()
                            ]
                        ),
                        json_encode(
                            [
                                "delete_at" => [
                                    "downloads" => 0,
                                    "days" => 0,
                                ],
                            ]
                        )
                    );
                }

                $files = $this->gateway->getFiles();

                echo $this->responedCreated($files[0]);
                break;
            default:
                $this->responedMethodNotAllowed(["POST"]);
                break;
        }
    }

    public function responedUnprocessableEntity(array $errors)
    {
        http_response_code(442);

        echo json_encode(["errors" => $errors]);
    }

    public function responedMethodNotAllowed(array $allowed_method): void
    {
        http_response_code(405);

        header("Allow: " . implode(", ", $allowed_method));
    }

    public function responedNotFound(string $id): void
    {
        http_response_code(404);

        echo json_encode([
            "id" => $id,
            "message" => "File not found"
        ]);
    }

    public function responedCreated(mixed $file): void
    {
        http_response_code(201);

        echo json_encode($file);
    }

    public function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data['name'])) {
            $errors[] = "name is required";
        }

        if (!(empty($data['priority']))) {
            if (filter_var($data['priority'], FILTER_VALIDATE_INT) === false) {
                $errors[] = "priority must be an integer";
            }
        }

        return $errors;
    }
}
