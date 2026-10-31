<?php  

function getAllModes($pdo) {
	$sql = "SELECT modes.mode_id, modes.mode_name, modes.description,
			CONCAT(updated_user.first_name, ' ', updated_user.last_name) AS updated_by_name
		FROM modes
		LEFT JOIN users AS updated_user ON modes.updated_by = updated_user.user_id
		ORDER BY modes.mode_id";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function insertMode($pdo, $mode_name, $description, $user_id) {

    $sql = "INSERT INTO modes
            (mode_name, description, added_by, updated_by)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $mode_name,
        $description,
        $user_id,
        $user_id
    ]);
}

function updateMode(
    $pdo,
    $mode_name,
    $description,
    $mode_id,
    $user_id
) {

    $sql = "UPDATE modes
            SET mode_name = ?,
                description = ?,
                updated_by = ?
            WHERE mode_id = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $mode_name,
        $description,
        $user_id,
        $mode_id
    ]);
}

function deleteMode($pdo, $mode_id) {
	$sql = "DELETE FROM modes WHERE mode_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$mode_id]);
}

function getAllRoutes($pdo) {
	$sql = "SELECT routes.route_id, routes.route_name, routes.origin,
			routes.destination, routes.mode_id, modes.mode_name,
			CONCAT(updated_user.first_name, ' ', updated_user.last_name) AS updated_by_name
		FROM routes
		JOIN modes ON routes.mode_id = modes.mode_id
		LEFT JOIN users AS updated_user ON routes.updated_by = updated_user.user_id
		ORDER BY routes.route_id";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function insertRoute(
    $pdo,
    $route_name,
    $origin,
    $destination,
    $mode_id,
    $user_id
) {

    $sql = "INSERT INTO routes
            (
                route_name,
                origin,
                destination,
                mode_id,
                added_by,
                updated_by
            )
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $route_name,
        $origin,
        $destination,
        $mode_id,
        $user_id,
        $user_id
    ]);
}

function updateRoute($pdo, $route_name, $origin, $destination, $mode_id, $route_id, $user_id) {
	$sql = "UPDATE routes
			SET route_name = ?, origin = ?, destination = ?, mode_id = ?, updated_by = ?
			WHERE route_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$route_name, $origin, $destination, $mode_id, $user_id, $route_id]);
}

function deleteRoute($pdo, $route_id) {
	$sql = "DELETE FROM routes WHERE route_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$route_id]);
}

function insertWebDev($pdo, $username, $first_name, $last_name, 
	$date_of_birth, $specialization) {

	$sql = "INSERT INTO web_devs (username, first_name, last_name, 
		date_of_birth, specialization) VALUES(?,?,?,?,?)";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $first_name, $last_name, 
		$date_of_birth, $specialization]);

	if ($executeQuery) {
		return true;
	}
}



function updateWebDev($pdo, $first_name, $last_name, 
	$date_of_birth, $specialization, $web_dev_id) {

	$sql = "UPDATE web_devs
				SET first_name = ?,
					last_name = ?,
					date_of_birth = ?, 
					specialization = ?
				WHERE web_dev_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$first_name, $last_name, 
		$date_of_birth, $specialization, $web_dev_id]);
	
	if ($executeQuery) {
		return true;
	}

}


function deleteWebDev($pdo, $web_dev_id) {
	$deleteWebDevProj = "DELETE FROM projects WHERE web_dev_id = ?";
	$deleteStmt = $pdo->prepare($deleteWebDevProj);
	$executeDeleteQuery = $deleteStmt->execute([$web_dev_id]);

	if ($executeDeleteQuery) {
		$sql = "DELETE FROM web_devs WHERE web_dev_id = ?";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$web_dev_id]);

		if ($executeQuery) {
			return true;
		}

	}
	
}




function getAllWebDevs($pdo) {
	$sql = "SELECT * FROM web_devs";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getWebDevByID($pdo, $web_dev_id) {
	$sql = "SELECT * FROM web_devs WHERE web_dev_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$web_dev_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}





function getProjectsByWebDev($pdo, $web_dev_id) {
	
	$sql = "SELECT 
				projects.project_id AS project_id,
				projects.project_name AS project_name,
				projects.technologies_used AS technologies_used,
				projects.date_added AS date_added,
				CONCAT(web_devs.first_name,' ',web_devs.last_name) AS project_owner
			FROM projects
			JOIN web_devs ON projects.web_dev_id = web_devs.web_dev_id
			WHERE projects.web_dev_id = ? 
			GROUP BY projects.project_name;
			";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$web_dev_id]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function insertProject($pdo, $project_name, $technologies_used, $web_dev_id) {
	$sql = "INSERT INTO projects (project_name, technologies_used, web_dev_id) VALUES (?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$project_name, $technologies_used, $web_dev_id]);
	if ($executeQuery) {
		return true;
	}

}

function getProjectByID($pdo, $project_id) {
	$sql = "SELECT 
				projects.project_id AS project_id,
				projects.project_name AS project_name,
				projects.technologies_used AS technologies_used,
				projects.date_added AS date_added,
				CONCAT(web_devs.first_name,' ',web_devs.last_name) AS project_owner
			FROM projects
			JOIN web_devs ON projects.web_dev_id = web_devs.web_dev_id
			WHERE projects.project_id  = ? 
			GROUP BY projects.project_name";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$project_id]);
	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function updateProject($pdo, $project_name, $technologies_used, $project_id) {
	$sql = "UPDATE projects
			SET project_name = ?,
				technologies_used = ?
			WHERE project_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$project_name, $technologies_used, $project_id]);

	if ($executeQuery) {
		return true;
	}
}

function deleteProject($pdo, $project_id) {
	$sql = "DELETE FROM projects WHERE project_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$project_id]);
	if ($executeQuery) {
		return true;
	}
}




?>