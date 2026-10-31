<?php

declare(strict_types=1);

namespace Files\ApiScenarios;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/ScenarioRun.php';

/**
 * Generated from verification/scenarios/api.json (SHA-256 5d9b8eff0ef0724a68a458db77dc85784bb7d24436f104b98c2a2fe67086bbe0), the Files.com API scenarios
 * every client shares, bound to swagger_doc-production.json (SHA-256 12e71f0e6ded092cf4206d22835cafb817efa9399cee170804bc1b4358c9f48d). Each test runs
 * one scenario's API operations and client actions through this SDK against the simulator api-scenarios.sh starts, in
 * a folder of its own (ScenarioRun). Steps only a Files.com site can answer are left out; a step this binding does not
 * carry out is named, and its test ends incomplete.
 */
class ApiScenarioTest extends TestCase
{
    const DEFINITION_SHA256 = '5d9b8eff0ef0724a68a458db77dc85784bb7d24436f104b98c2a2fe67086bbe0';
    const SCHEMA_SHA256 = '12e71f0e6ded092cf4206d22835cafb817efa9399cee170804bc1b4358c9f48d';

    /**
     * Each scenario's case, by test method: its scenario, its number of steps, the steps only a site answers, the steps
     * this binding carries out, and the steps it does not, each with why and the later steps that need it.
     */
    public static function cases()
    {
        return [
            'testFilesOwnedFolderNamespace' => [
                'scenario' => 'files.owned-folder-namespace',
                'steps' => 25,
                'site' => [3, 6],
                'carried_out' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25],
                'unimplemented' => [
                ],
            ],
            'testFilesOwnedBinaryRoundtrip' => [
                'scenario' => 'files.owned-binary-roundtrip',
                'steps' => 24,
                'site' => [3, 6],
                'carried_out' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
                'unimplemented' => [
                ],
            ],
            'testFilesOwnedNamespaceExtended' => [
                'scenario' => 'files.owned-namespace-extended',
                'steps' => 48,
                'site' => [3, 6],
                'carried_out' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48],
                'unimplemented' => [
                ],
            ],
            'testFilesOwnedDirectoryHelpers' => [
                'scenario' => 'files.owned-directory-helpers',
                'steps' => 44,
                'site' => [3, 6],
                'carried_out' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44],
                'unimplemented' => [
                ],
            ],
        ];
    }

    // files.owned-folder-namespace: Folders made one at a time inside the owned folder: each found by path, an existing name refused, the owned folder listed one entry per page with exactly its own children, and every folder deleted and confirmed absent. API operations only.
    public function testFilesOwnedFolderNamespace()
    {
        $cases = self::cases();
        ScenarioRun::run($this, 'testFilesOwnedFolderNamespace', $cases['testFilesOwnedFolderNamespace'], function ($run) {
            // Step 1 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(1, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\BadRequest\RequestParamsRequiredException::class, 422, 'bad-request/request-params-required', function () use ($run) {
                    \Files\Model\File::find('/', []);
                });
            });

            // Step 2 (FolderListForPath, GET /folders/{path})
            $run->step(2, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor('/', ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
            });

            // Step 3 (BehaviorListForPath, GET /behaviors/folders/{path}) is for a Files.com site only.

            // Step 4 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(4, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });

            // Step 5 (PostFoldersPath, POST /folders/{path})
            $run->step(5, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path(''), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 6 (FileActionFind, GET /file_actions/metadata/{path}) is for a Files.com site only.

            // Step 7 (PostFoldersPath, POST /folders/{path})
            $run->step(7, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path('alpha'), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('alpha')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 8 (PostFoldersPath, POST /folders/{path})
            $run->step(8, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path('bravo'), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('bravo')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 9 (PostFoldersPath, POST /folders/{path})
            $run->step(9, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path('charlie'), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('charlie')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 10 (PostFoldersPath, POST /folders/{path})
            $run->step(10, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path('alpha/inner'), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('alpha/inner')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 11 (PostFoldersPath, POST /folders/{path})
            $run->step(11, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\ProcessingFailure\DestinationExistsException::class, 422, 'processing-failure/destination-exists', function () use ($run) {
                    \Files\Model\Folder::create($run->path('alpha'), ['mkdir_parents' => false]);
                });
            });

            // Step 12 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(12, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('alpha'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('alpha')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 13 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(13, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('alpha/inner'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('alpha/inner')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 14 (FolderListForPath, GET /folders/{path})
            $run->step(14, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path(''), ['per_page' => 1]);
                }, 200, 1, 16, 0);
                $run->sameEntries($records, ['directory ' . $run->path('alpha'), 'directory ' . $run->path('bravo'), 'directory ' . $run->path('charlie')]);
            });

            // Step 15 (FolderListForPath, GET /folders/{path})
            $run->step(15, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('alpha'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['directory ' . $run->path('alpha/inner')]);
            });

            // Step 16 (DeleteFilesPath, DELETE /files/{path})
            $run->step(16, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('alpha/inner')]))->delete(['recursive' => false]);
                });
            });

            // Step 17 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(17, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('alpha/inner'), []);
                });
            });

            // Step 18 (DeleteFilesPath, DELETE /files/{path})
            $run->step(18, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('alpha')]))->delete(['recursive' => false]);
                });
            });

            // Step 19 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(19, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('alpha'), []);
                });
            });

            // Step 20 (DeleteFilesPath, DELETE /files/{path})
            $run->step(20, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('bravo')]))->delete(['recursive' => false]);
                });
            });

            // Step 21 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(21, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('bravo'), []);
                });
            });

            // Step 22 (DeleteFilesPath, DELETE /files/{path})
            $run->step(22, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('charlie')]))->delete(['recursive' => false]);
                });
            });

            // Step 23 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(23, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('charlie'), []);
                });
            });

            // Step 24 (DeleteFilesPath, DELETE /files/{path})
            $run->step(24, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('')]))->delete(['recursive' => false]);
                });
            });

            // Step 25 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(25, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });
        });
    }

    // files.owned-binary-roundtrip: Three owned files (from a local file, a seekable stream and a forward-only stream giving at most three bytes per read) uploaded with a provided mtime, each found with its exact size, the owned folder listed one entry per page with exactly those files, each downloaded byte for byte, and everything deleted and confirmed absent. Conserved from verification/files-paired/contract.json revision 3 and its journeys.
    public function testFilesOwnedBinaryRoundtrip()
    {
        $cases = self::cases();
        ScenarioRun::run($this, 'testFilesOwnedBinaryRoundtrip', $cases['testFilesOwnedBinaryRoundtrip'], function ($run) {
            // Step 1 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(1, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\BadRequest\RequestParamsRequiredException::class, 422, 'bad-request/request-params-required', function () use ($run) {
                    \Files\Model\File::find('/', []);
                });
            });

            // Step 2 (FolderListForPath, GET /folders/{path})
            $run->step(2, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor('/', ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
            });

            // Step 3 (BehaviorListForPath, GET /behaviors/folders/{path}) is for a Files.com site only.

            // Step 4 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(4, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });

            // Step 5 (PostFoldersPath, POST /folders/{path})
            $run->step(5, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path(''), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 6 (FileActionFind, GET /file_actions/metadata/{path}) is for a Files.com site only.

            // Step 7 (upload binary.bin)
            $run->step(7, 'upload binary.bin', function () use ($run) {
                $run->upload($run->path('binary.bin'), $run->recipe(1, 131, 17, 251, 65539, 'b65094d36ac1009dac9c73c27b48638a47eb7f23d219a80d1b4d05b58f514499'), 'local-file', 0, ['provided_mtime' => '2026-09-24T00:00:00Z'], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 8 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(8, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('binary.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('binary.bin')]);
                $run->oneOf('size', $entry->getSize(), [65539]);
                $run->integer($body, 'size', 65539);
                $run->field('provided_mtime', 'date-time', $entry->getProvidedMtime(), ['one_of' => ['2026-09-24T00:00:00Z', null]]);
            });

            // Step 9 (upload empty.bin)
            $run->step(9, 'upload empty.bin', function () use ($run) {
                $run->upload($run->path('empty.bin'), $run->recipe(2, 1, 0, 256, 0, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'), 'seekable-stream', 0, ['provided_mtime' => '2026-09-24T00:00:00Z'], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadData($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 10 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(10, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('empty.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('empty.bin')]);
                $run->oneOf('size', $entry->getSize(), [0]);
                $run->integer($body, 'size', 0);
                $run->field('provided_mtime', 'date-time', $entry->getProvidedMtime(), ['one_of' => ['2026-09-24T00:00:00Z', null]]);
            });

            // Step 11 (upload short-read.bin)
            $run->step(11, 'upload short-read.bin', function () use ($run) {
                $run->upload($run->path('short-read.bin'), $run->recipe(3, 29, 101, 256, 65537, 'ed5a86d5b614d5385a85c170c15f25fe75c3faab6a8b29abbb470a6c51faa7f9'), 'forward-only-stream', 3, ['provided_mtime' => '2026-09-24T00:00:00Z'], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadData($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 12 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(12, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('short-read.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('short-read.bin')]);
                $run->oneOf('size', $entry->getSize(), [65537]);
                $run->integer($body, 'size', 65537);
                $run->field('provided_mtime', 'date-time', $entry->getProvidedMtime(), ['one_of' => ['2026-09-24T00:00:00Z', null]]);
            });

            // Step 13 (FolderListForPath, GET /folders/{path})
            $run->step(13, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path(''), ['per_page' => 1]);
                }, 200, 1, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('binary.bin'), 'file ' . $run->path('empty.bin'), 'file ' . $run->path('short-read.bin')]);
            });

            // Step 14 (download binary.bin)
            $run->step(14, 'download binary.bin', function () use ($run) {
                $run->download($run->path('binary.bin'), $run->recipe(1, 131, 17, 251, 65539, 'b65094d36ac1009dac9c73c27b48638a47eb7f23d219a80d1b4d05b58f514499'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 15 (download empty.bin)
            $run->step(15, 'download empty.bin', function () use ($run) {
                $run->download($run->path('empty.bin'), $run->recipe(2, 1, 0, 256, 0, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 16 (download short-read.bin)
            $run->step(16, 'download short-read.bin', function () use ($run) {
                $run->download($run->path('short-read.bin'), $run->recipe(3, 29, 101, 256, 65537, 'ed5a86d5b614d5385a85c170c15f25fe75c3faab6a8b29abbb470a6c51faa7f9'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 17 (DeleteFilesPath, DELETE /files/{path})
            $run->step(17, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('binary.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 18 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(18, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('binary.bin'), []);
                });
            });

            // Step 19 (DeleteFilesPath, DELETE /files/{path})
            $run->step(19, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('empty.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 20 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(20, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('empty.bin'), []);
                });
            });

            // Step 21 (DeleteFilesPath, DELETE /files/{path})
            $run->step(21, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('short-read.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 22 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(22, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('short-read.bin'), []);
                });
            });

            // Step 23 (DeleteFilesPath, DELETE /files/{path})
            $run->step(23, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('')]))->delete(['recursive' => false]);
                });
            });

            // Step 24 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(24, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });
        });
    }

    // files.owned-namespace-extended: Literal special-character names (#, %, a non-ASCII letter and a space), a nested folder, a multipart upload stored in at least two parts with its final progress, a copy and a move waited for, a one-entry-per-page listing whose returned entries download their own files, byte-exact downloads, an upload cancelled after its first acknowledged part that publishes nothing, and non-recursive deletes confirmed by absence. Conserved from verification/files-paired/contract-extended.json revision 2 and its journeys.
    public function testFilesOwnedNamespaceExtended()
    {
        $cases = self::cases();
        ScenarioRun::run($this, 'testFilesOwnedNamespaceExtended', $cases['testFilesOwnedNamespaceExtended'], function ($run) {
            // Step 1 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(1, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\BadRequest\RequestParamsRequiredException::class, 422, 'bad-request/request-params-required', function () use ($run) {
                    \Files\Model\File::find('/', []);
                });
            });

            // Step 2 (FolderListForPath, GET /folders/{path})
            $run->step(2, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor('/', ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
            });

            // Step 3 (BehaviorListForPath, GET /behaviors/folders/{path}) is for a Files.com site only.

            // Step 4 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(4, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });

            // Step 5 (PostFoldersPath, POST /folders/{path})
            $run->step(5, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path(''), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 6 (FileActionFind, GET /file_actions/metadata/{path}) is for a Files.com site only.

            // Step 7 (upload hash-name)
            $run->step(7, 'upload hash-name', function () use ($run) {
                $run->upload($run->path('a#b.txt'), $run->recipe(4, 37, 5, 256, 1031, '29f483b8739ae2de994d4794133a7a858a0f0416caa03e5a96165af41ebdf3e8'), 'local-file', 0, [], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 8 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(8, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('a#b.txt'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('a#b.txt')]);
                $run->oneOf('size', $entry->getSize(), [1031]);
                $run->integer($body, 'size', 1031);
            });

            // Step 9 (upload percent-name)
            $run->step(9, 'upload percent-name', function () use ($run) {
                $run->upload($run->path('100%.txt'), $run->recipe(5, 41, 11, 256, 2053, '83298104fe01d905ee9ce8fae2f23da08007b5e8ab356d5bfc064b49c2664b98'), 'local-file', 0, [], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 10 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(10, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('100%.txt'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('100%.txt')]);
                $run->oneOf('size', $entry->getSize(), [2053]);
                $run->integer($body, 'size', 2053);
            });

            // Step 11 (upload unicode-name)
            $run->step(11, 'upload unicode-name', function () use ($run) {
                $run->upload($run->path('é +.txt'), $run->recipe(6, 43, 3, 256, 517, 'b664357b3151e48ca4c5c6c452dd20d58293897f887536f6e0e0627c0d8cd25b'), 'local-file', 0, [], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 12 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(12, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('é +.txt'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('é +.txt')]);
                $run->oneOf('size', $entry->getSize(), [517]);
                $run->integer($body, 'size', 517);
            });

            // Step 13 (PostFoldersPath, POST /folders/{path})
            $run->step(13, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path('nested'), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('nested')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 14 (upload inner.bin)
            $run->step(14, 'upload inner.bin', function () use ($run) {
                $run->upload($run->path('nested/inner.bin'), $run->recipe(7, 47, 19, 256, 4099, '9f251477458622d9da868e31b8708ec638d8ba9a01c9b575bb8c0a0764734521'), 'seekable-stream', 0, [], 0, false, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadData($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 15 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(15, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('nested/inner.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('nested/inner.bin')]);
                $run->oneOf('size', $entry->getSize(), [4099]);
                $run->integer($body, 'size', 4099);
            });

            // Step 16 (upload large.bin)
            $run->step(16, 'upload large.bin', function () use ($run) {
                $run->upload($run->path('large.bin'), $run->recipe(8, 53, 23, 251, 6291459, '4ab1615925cb4b1fa9b9ddc3345a920200888d8ba7d3ae1b880aa6c0c19f87fa'), 'local-file', 0, [], 2, true, [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 17 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(17, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('large.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('large.bin')]);
                $run->oneOf('size', $entry->getSize(), [6291459]);
                $run->integer($body, 'size', 6291459);
            });

            // Step 18 (copy inner.bin to copied.bin)
            $run->step(18, 'copy inner.bin to copied.bin', function () use ($run) {
                $action = (new \Files\Model\File(['path' => $run->path('nested/inner.bin')]))->copy(['destination' => $run->path('copied.bin')]);
                $run->completed($action->getStatus(), $action->getFileMigrationId(), function ($id) {
                    return \Files\Model\FileMigration::find($id)->getStatus();
                });
            });

            // Step 19 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(19, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('copied.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('copied.bin')]);
                $run->oneOf('size', $entry->getSize(), [4099]);
                $run->integer($body, 'size', 4099);
            });

            // Step 20 (move copied.bin to moved.bin)
            $run->step(20, 'move copied.bin to moved.bin', function () use ($run) {
                $action = (new \Files\Model\File(['path' => $run->path('copied.bin')]))->move(['destination' => $run->path('nested/moved.bin')]);
                $run->completed($action->getStatus(), $action->getFileMigrationId(), function ($id) {
                    return \Files\Model\FileMigration::find($id)->getStatus();
                });
            });

            // Step 21 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(21, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('copied.bin'), []);
                });
            });

            // Step 22 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(22, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('nested/moved.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('nested/moved.bin')]);
                $run->oneOf('size', $entry->getSize(), [4099]);
                $run->integer($body, 'size', 4099);
            });

            // Step 23 (FolderListForPath, GET /folders/{path})
            $run->step(23, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path(''), ['per_page' => 1]);
                }, 200, 1, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('a#b.txt'), 'file ' . $run->path('100%.txt'), 'file ' . $run->path('é +.txt'), 'directory ' . $run->path('nested'), 'file ' . $run->path('large.bin')]);
                $run->listed($records);
            });

            // Step 24 (download hash-name)
            $run->step(24, 'download hash-name', function () use ($run) {
                $run->download($run->path('a#b.txt'), $run->recipe(4, 37, 5, 256, 1031, '29f483b8739ae2de994d4794133a7a858a0f0416caa03e5a96165af41ebdf3e8'), true, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 25 (download percent-name)
            $run->step(25, 'download percent-name', function () use ($run) {
                $run->download($run->path('100%.txt'), $run->recipe(5, 41, 11, 256, 2053, '83298104fe01d905ee9ce8fae2f23da08007b5e8ab356d5bfc064b49c2664b98'), true, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 26 (download unicode-name)
            $run->step(26, 'download unicode-name', function () use ($run) {
                $run->download($run->path('é +.txt'), $run->recipe(6, 43, 3, 256, 517, 'b664357b3151e48ca4c5c6c452dd20d58293897f887536f6e0e0627c0d8cd25b'), true, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 27 (download large.bin)
            $run->step(27, 'download large.bin', function () use ($run) {
                $run->download($run->path('large.bin'), $run->recipe(8, 53, 23, 251, 6291459, '4ab1615925cb4b1fa9b9ddc3345a920200888d8ba7d3ae1b880aa6c0c19f87fa'), true, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 28 (download inner.bin)
            $run->step(28, 'download inner.bin', function () use ($run) {
                $run->download($run->path('nested/inner.bin'), $run->recipe(7, 47, 19, 256, 4099, '9f251477458622d9da868e31b8708ec638d8ba9a01c9b575bb8c0a0764734521'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 29 (download moved.bin)
            $run->step(29, 'download moved.bin', function () use ($run) {
                $run->download($run->path('nested/moved.bin'), $run->recipe(7, 47, 19, 256, 4099, '9f251477458622d9da868e31b8708ec638d8ba9a01c9b575bb8c0a0764734521'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 30 (download large.bin)
            $run->step(30, 'download large.bin', function () use ($run) {
                $run->download($run->path('large.bin'), $run->recipe(8, 53, 23, 251, 6291459, '4ab1615925cb4b1fa9b9ddc3345a920200888d8ba7d3ae1b880aa6c0c19f87fa'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 31 (upload cancelled.bin)
            $run->step(31, 'upload cancelled.bin', function () use ($run) {
                $run->cancelledUpload($run->path('cancelled.bin'), $run->recipe(9, 59, 29, 251, 6291461, 'd7a71574a24faecb2fea4915b8096a86a43b19db98d440f16730caac0a66cfd4'), 'local-file', 0, [], [
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 32 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(32, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('cancelled.bin'), []);
                });
            });

            // Step 33 (DeleteFilesPath, DELETE /files/{path})
            $run->step(33, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('nested/moved.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 34 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(34, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('nested/moved.bin'), []);
                });
            });

            // Step 35 (DeleteFilesPath, DELETE /files/{path})
            $run->step(35, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('nested/inner.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 36 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(36, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('nested/inner.bin'), []);
                });
            });

            // Step 37 (DeleteFilesPath, DELETE /files/{path})
            $run->step(37, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('nested')]))->delete(['recursive' => false]);
                });
            });

            // Step 38 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(38, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('nested'), []);
                });
            });

            // Step 39 (DeleteFilesPath, DELETE /files/{path})
            $run->step(39, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('large.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 40 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(40, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('large.bin'), []);
                });
            });

            // Step 41 (DeleteFilesPath, DELETE /files/{path})
            $run->step(41, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('a#b.txt')]))->delete(['recursive' => false]);
                });
            });

            // Step 42 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(42, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('a#b.txt'), []);
                });
            });

            // Step 43 (DeleteFilesPath, DELETE /files/{path})
            $run->step(43, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('100%.txt')]))->delete(['recursive' => false]);
                });
            });

            // Step 44 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(44, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('100%.txt'), []);
                });
            });

            // Step 45 (DeleteFilesPath, DELETE /files/{path})
            $run->step(45, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('é +.txt')]))->delete(['recursive' => false]);
                });
            });

            // Step 46 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(46, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('é +.txt'), []);
                });
            });

            // Step 47 (DeleteFilesPath, DELETE /files/{path})
            $run->step(47, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('')]))->delete(['recursive' => false]);
                });
            });

            // Step 48 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(48, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });
        });
    }

    // files.owned-directory-helpers: A local tree uploaded as a directory, each entry it made found and each folder listed with exactly its own entries, the tree downloaded as a directory byte for byte, a folder copied and the copy moved (each waited for), the moved file downloaded, and non-recursive deletes, deepest first, confirmed by absence. Conserved from verification/files-paired/contract-helpers.json revision 1 and its journeys.
    public function testFilesOwnedDirectoryHelpers()
    {
        $cases = self::cases();
        ScenarioRun::run($this, 'testFilesOwnedDirectoryHelpers', $cases['testFilesOwnedDirectoryHelpers'], function ($run) {
            // Step 1 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(1, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\BadRequest\RequestParamsRequiredException::class, 422, 'bad-request/request-params-required', function () use ($run) {
                    \Files\Model\File::find('/', []);
                });
            });

            // Step 2 (FolderListForPath, GET /folders/{path})
            $run->step(2, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor('/', ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
            });

            // Step 3 (BehaviorListForPath, GET /behaviors/folders/{path}) is for a Files.com site only.

            // Step 4 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(4, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->failsWith(\Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });

            // Step 5 (PostFoldersPath, POST /folders/{path})
            $run->step(5, 'PostFoldersPath, POST /folders/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(201, function () use ($run) {
                    return \Files\Model\Folder::create($run->path(''), ['mkdir_parents' => false]);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 6 (FileActionFind, GET /file_actions/metadata/{path}) is for a Files.com site only.

            // Step 7 (upload-directory up)
            $run->step(7, 'upload-directory up', function () use ($run) {
                $run->uploadDirectory($run->path('up'), [
                    ['a.bin', $run->recipe(10, 61, 7, 256, 1543, '983f31356cea338492c47d6c9c979deb3a45914fe6aa8eafa96cd44e02f08e9d')],
                    ['sub', null],
                    ['sub/b.bin', $run->recipe(11, 67, 13, 256, 2089, '03e528690696066e61d8a65fa0fac38974bb95cb0d31874240287f325484fddb')],
                    ['empty', null],
                ], ['a.bin', 'sub/b.bin'], 2, 3632, ['empty', 'sub'], ['empty'], 1, [
                    'folder' => function ($path) {
                        return \Files\Model\Folder::create($path);
                    },
                    'created' => function ($model) {
                        return [
                            'path' => $model->getPath(),
                            'type' => $model->getType(),
                        ];
                    },
                    'upload' => function ($path, $source, array $params, array $options) {
                        return \Files\Model\File::uploadFile($path, $source, $params, $options);
                    },
                ]);
            });

            // Step 8 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(8, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('up'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('up')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 9 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(9, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('up/a.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('up/a.bin')]);
                $run->oneOf('size', $entry->getSize(), [1543]);
                $run->integer($body, 'size', 1543);
            });

            // Step 10 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(10, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('up/sub'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('up/sub')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 11 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(11, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('up/sub/b.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('up/sub/b.bin')]);
                $run->oneOf('size', $entry->getSize(), [2089]);
                $run->integer($body, 'size', 2089);
            });

            // Step 12 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(12, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('up/empty'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('up/empty')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 13 (FolderListForPath, GET /folders/{path})
            $run->step(13, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('up'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('up/a.bin'), 'directory ' . $run->path('up/sub'), 'directory ' . $run->path('up/empty')]);
            });

            // Step 14 (FolderListForPath, GET /folders/{path})
            $run->step(14, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('up/sub'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('up/sub/b.bin')]);
            });

            // Step 15 (FolderListForPath, GET /folders/{path})
            $run->step(15, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('up/empty'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, []);
            });

            // Step 16 (download-directory up)
            $run->step(16, 'download-directory up', function () use ($run) {
                $run->downloadDirectory($run->path('up'), [
                    ['a.bin', $run->recipe(10, 61, 7, 256, 1543, '983f31356cea338492c47d6c9c979deb3a45914fe6aa8eafa96cd44e02f08e9d')],
                    ['sub/b.bin', $run->recipe(11, 67, 13, 256, 2089, '03e528690696066e61d8a65fa0fac38974bb95cb0d31874240287f325484fddb')],
                ], 2, 1, 3632, [
                    'list' => function ($path) {
                        return \Files\Model\Folder::listFor($path);
                    },
                    'entry' => function ($model) {
                        return [
                            'path' => $model->getPath(),
                            'type' => $model->getType(),
                            'size' => $model->getSize(),
                        ];
                    },
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 17 (copy up-sub to copy)
            $run->step(17, 'copy up-sub to copy', function () use ($run) {
                $action = (new \Files\Model\File(['path' => $run->path('up/sub')]))->copy(['destination' => $run->path('copy')]);
                $run->completed($action->getStatus(), $action->getFileMigrationId(), function ($id) {
                    return \Files\Model\FileMigration::find($id)->getStatus();
                });
            });

            // Step 18 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(18, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('copy'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('copy')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 19 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(19, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('copy/b.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('copy/b.bin')]);
                $run->oneOf('size', $entry->getSize(), [2089]);
                $run->integer($body, 'size', 2089);
            });

            // Step 20 (FolderListForPath, GET /folders/{path})
            $run->step(20, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('copy'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('copy/b.bin')]);
            });

            // Step 21 (move copy to moved)
            $run->step(21, 'move copy to moved', function () use ($run) {
                $action = (new \Files\Model\File(['path' => $run->path('copy')]))->move(['destination' => $run->path('moved')]);
                $run->completed($action->getStatus(), $action->getFileMigrationId(), function ($id) {
                    return \Files\Model\FileMigration::find($id)->getStatus();
                });
            });

            // Step 22 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(22, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('copy/b.bin'), []);
                });
            });

            // Step 23 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(23, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('copy'), []);
                });
            });

            // Step 24 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(24, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('moved'), []);
                });
                $run->oneOf('type', $entry->getType(), ['directory']);
                $run->oneOf('path', $entry->getPath(), [$run->path('moved')]);
                $run->oneOf('size', $entry->getSize(), [null]);
                $run->noValue($body, 'size');
            });

            // Step 25 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(25, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                list($entry, $body) = $run->succeeds(200, function () use ($run) {
                    return \Files\Model\File::find($run->path('moved/b.bin'), []);
                });
                $run->oneOf('type', $entry->getType(), ['file']);
                $run->oneOf('path', $entry->getPath(), [$run->path('moved/b.bin')]);
                $run->oneOf('size', $entry->getSize(), [2089]);
                $run->integer($body, 'size', 2089);
            });

            // Step 26 (FolderListForPath, GET /folders/{path})
            $run->step(26, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path('moved'), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['file ' . $run->path('moved/b.bin')]);
            });

            // Step 27 (FolderListForPath, GET /folders/{path})
            $run->step(27, 'FolderListForPath, GET /folders/{path}', function () use ($run) {
                $records = $run->pages(function () use ($run) {
                    return \Files\Model\Folder::listFor($run->path(''), ['per_page' => 1000]);
                }, 200, 1000, 16, 0);
                $run->sameEntries($records, ['directory ' . $run->path('up'), 'directory ' . $run->path('moved')]);
            });

            // Step 28 (download moved-b)
            $run->step(28, 'download moved-b', function () use ($run) {
                $run->download($run->path('moved/b.bin'), $run->recipe(11, 67, 13, 256, 2089, '03e528690696066e61d8a65fa0fac38974bb95cb0d31874240287f325484fddb'), false, [
                    'download' => function ($path, $local) {
                        return \Files\Model\File::downloadToFile($path, $local);
                    },
                ]);
            });

            // Step 29 (DeleteFilesPath, DELETE /files/{path})
            $run->step(29, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('moved/b.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 30 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(30, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('moved/b.bin'), []);
                });
            });

            // Step 31 (DeleteFilesPath, DELETE /files/{path})
            $run->step(31, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('moved')]))->delete(['recursive' => false]);
                });
            });

            // Step 32 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(32, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('moved'), []);
                });
            });

            // Step 33 (DeleteFilesPath, DELETE /files/{path})
            $run->step(33, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('up/sub/b.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 34 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(34, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('up/sub/b.bin'), []);
                });
            });

            // Step 35 (DeleteFilesPath, DELETE /files/{path})
            $run->step(35, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('up/sub')]))->delete(['recursive' => false]);
                });
            });

            // Step 36 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(36, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('up/sub'), []);
                });
            });

            // Step 37 (DeleteFilesPath, DELETE /files/{path})
            $run->step(37, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('up/empty')]))->delete(['recursive' => false]);
                });
            });

            // Step 38 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(38, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('up/empty'), []);
                });
            });

            // Step 39 (DeleteFilesPath, DELETE /files/{path})
            $run->step(39, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('up/a.bin')]))->delete(['recursive' => false]);
                });
            });

            // Step 40 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(40, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('up/a.bin'), []);
                });
            });

            // Step 41 (DeleteFilesPath, DELETE /files/{path})
            $run->step(41, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('up')]))->delete(['recursive' => false]);
                });
            });

            // Step 42 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(42, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path('up'), []);
                });
            });

            // Step 43 (DeleteFilesPath, DELETE /files/{path})
            $run->step(43, 'DeleteFilesPath, DELETE /files/{path}', function () use ($run) {
                $run->noContent(204, function () use ($run) {
                    (new \Files\Model\File(['path' => $run->path('')]))->delete(['recursive' => false]);
                });
            });

            // Step 44 (FileActionFind, GET /file_actions/metadata/{path})
            $run->step(44, 'FileActionFind, GET /file_actions/metadata/{path}', function () use ($run) {
                $run->eventuallyFailsWith(5, 1, \Files\Exception\NotFoundException::class, 404, 'not-found', function () use ($run) {
                    \Files\Model\File::find($run->path(''), []);
                });
            });
        });
    }
}
