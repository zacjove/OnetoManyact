<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /OnetoManyact/login.php');
    exit;
}

require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/models.php';

$modeFormMessage = null;
$routeFormMessage = null;
$currentUserId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modeAction'])) {
    $modeAction = $_POST['modeAction'];

    try {
        if ($modeAction === 'insert') {
            $modeName = trim($_POST['mode_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($modeName === '') {
                throw new InvalidArgumentException('Please enter a mode name.');
            }

            insertMode($pdo, $modeName, $description, $currentUserId);
        } elseif ($modeAction === 'update') {
            $modeId = filter_input(INPUT_POST, 'mode_id', FILTER_VALIDATE_INT);
            $modeName = trim($_POST['mode_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (!$modeId || $modeName === '') {
                throw new InvalidArgumentException('A valid mode ID and mode name are required.');
            }

            updateMode($pdo, $modeName, $description, $modeId, $currentUserId);
        } elseif ($modeAction === 'delete') {
            $modeId = filter_input(INPUT_POST, 'mode_id', FILTER_VALIDATE_INT);

            if (!$modeId) {
                throw new InvalidArgumentException('A valid mode ID is required.');
            }

            deleteMode($pdo, $modeId);
        } else {
            throw new InvalidArgumentException('Unknown mode action.');
        }

        header('Location: /OnetoManyact/index.php?status=' . urlencode($modeAction));
        exit;
    } catch (InvalidArgumentException $exception) {
        $modeFormMessage = $exception->getMessage();
    } catch (PDOException $exception) {
        $modeFormMessage = ($modeAction === 'delete')
            ? 'Unable to delete this mode. Remove or reassign its routes first.'
            : 'Unable to save the mode. Check the values and try again.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['routeAction'])) {
    $routeAction = $_POST['routeAction'];

    try {
        if ($routeAction === 'insert') {
            $routeName = trim($_POST['route_name'] ?? '');
            $origin = trim($_POST['origin'] ?? '');
            $destination = trim($_POST['destination'] ?? '');
            $modeId = filter_input(INPUT_POST, 'mode_id', FILTER_VALIDATE_INT);

            if ($routeName === '' || $origin === '' || $destination === '' || !$modeId) {
                throw new InvalidArgumentException('Complete all route fields and select a valid mode.');
            }

            insertRoute(
                $pdo,
                $routeName,
                $origin,
                $destination,
                $modeId,
                $currentUserId
            );
        } elseif ($routeAction === 'update') {
            $routeId = filter_input(INPUT_POST, 'route_id', FILTER_VALIDATE_INT);
            $routeName = trim($_POST['route_name'] ?? '');
            $origin = trim($_POST['origin'] ?? '');
            $destination = trim($_POST['destination'] ?? '');
            $modeId = filter_input(INPUT_POST, 'mode_id', FILTER_VALIDATE_INT);

            if (!$routeId || $routeName === '' || $origin === '' || $destination === '' || !$modeId) {
                throw new InvalidArgumentException('Complete all route fields and select valid route and mode IDs.');
            }

            updateRoute(
                $pdo,
                $routeName,
                $origin,
                $destination,
                $modeId,
                $routeId,
                $currentUserId
            );
        } elseif ($routeAction === 'delete') {
            $routeId = filter_input(INPUT_POST, 'route_id', FILTER_VALIDATE_INT);

            if (!$routeId) {
                throw new InvalidArgumentException('A valid route ID is required.');
            }

            deleteRoute($pdo, $routeId);
        } else {
            throw new InvalidArgumentException('Unknown route action.');
        }

        header('Location: /OnetoManyact/index.php?status=route_' . urlencode($routeAction));
        exit;
    } catch (InvalidArgumentException $exception) {
        $routeFormMessage = $exception->getMessage();
    } catch (PDOException $exception) {
        $routeFormMessage = ($routeAction === 'delete')
            ? 'Unable to delete this route. Check whether other records depend on it.'
            : 'Unable to save the route. Check the values and selected mode.';
    }
}

if (isset($_POST['insertWebDevBtn'])) {
    $query = insertWebDev(
        $pdo,
        $_POST['username'] ?? '',
        $_POST['firstName'] ?? '',
        $_POST['lastName'] ?? '',
        $_POST['dateOfBirth'] ?? null,
        $_POST['specialization'] ?? ''
    );

    if ($query) {
        header('Location: /OnetoManyact/index.php');
        exit;
    }

    echo 'Insertion failed';
}

if (isset($_POST['editWebDevBtn'])) {
    $webDevId = filter_input(INPUT_GET, 'web_dev_id', FILTER_VALIDATE_INT);

    if ($webDevId) {
        $query = updateWebDev(
            $pdo,
            $_POST['firstName'] ?? '',
            $_POST['lastName'] ?? '',
            $_POST['dateOfBirth'] ?? null,
            $_POST['specialization'] ?? '',
            $webDevId
        );

        if ($query) {
            header('Location: /OnetoManyact/index.php');
            exit;
        }
    }

    echo 'Edit failed';
}

if (isset($_POST['deleteWebDevBtn'])) {
    $webDevId = filter_input(INPUT_GET, 'web_dev_id', FILTER_VALIDATE_INT);

    if ($webDevId && deleteWebDev($pdo, $webDevId)) {
        header('Location: /OnetoManyact/index.php');
        exit;
    }

    echo 'Deletion failed';
}

if (isset($_POST['insertNewProjectBtn'])) {
    $webDevId = filter_input(INPUT_GET, 'web_dev_id', FILTER_VALIDATE_INT);

    if ($webDevId) {
        $query = insertProject(
            $pdo,
            $_POST['projectName'] ?? '',
            $_POST['technologiesUsed'] ?? '',
            $webDevId
        );

        if ($query) {
            header('Location: /OnetoManyact/viewprojects.php?web_dev_id=' . $webDevId);
            exit;
        }
    }

    echo 'Insertion failed';
}

if (isset($_POST['editProjectBtn'])) {
    $projectId = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
    $webDevId = filter_input(INPUT_GET, 'web_dev_id', FILTER_VALIDATE_INT);

    if ($projectId && $webDevId) {
        $query = updateProject(
            $pdo,
            $_POST['projectName'] ?? '',
            $_POST['technologiesUsed'] ?? '',
            $projectId
        );

        if ($query) {
            header('Location: /OnetoManyact/viewprojects.php?web_dev_id=' . $webDevId);
            exit;
        }
    }

    echo 'Update failed';
}

if (isset($_POST['deleteProjectBtn'])) {
    $projectId = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
    $webDevId = filter_input(INPUT_GET, 'web_dev_id', FILTER_VALIDATE_INT);

    if ($projectId && $webDevId && deleteProject($pdo, $projectId)) {
        header('Location: /OnetoManyact/viewprojects.php?web_dev_id=' . $webDevId);
        exit;
    }

    echo 'Deletion failed';
}
