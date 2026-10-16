<?php
/**
 * Creates (or resets) a LOCAL-ONLY CMS superadmin for testing.
 * Credentials are written to .localdev/local-admin.txt.
 *
 *   php .localdev/seed-local-admin.php
 *
 * Never run this against a production database.
 */
$host = getenv('DB_HOST') ?: '127.0.0.1';
if ( ! in_array($host, array('127.0.0.1', 'localhost', '::1'), TRUE))
{
	fwrite(STDERR, "Refusing to seed a non-local database ($host).\n");
	exit(1);
}

$db = new mysqli($host, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', getenv('DB_NAME') ?: 'zedcms');
$email = 'localadmin@zed.test';
$password = bin2hex(random_bytes(8));
// Ion Auth is configured for bcrypt with 8 rounds ($2y$08$...)
$hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 8));

$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row)
{
	$id = (int) $row['id'];
	$stmt = $db->prepare('UPDATE users SET password = ?, active = 1 WHERE id = ?');
	$stmt->bind_param('si', $hash, $id);
	$stmt->execute();
}
else
{
	$now = time();
	$stmt = $db->prepare("INSERT INTO users (ip_address, username, password, email, created_on, active, first_name, last_name, company, phone)
		VALUES ('127.0.0.1', 'localadmin', ?, ?, ?, 1, 'Local', 'Admin', 'Local', '0')");
	$stmt->bind_param('ssi', $hash, $email, $now);
	$stmt->execute();
	$id = $db->insert_id;
	$db->query("INSERT INTO users_groups (user_id, group_id) SELECT $id, id FROM `groups` WHERE name = 'superadmin'");
}

file_put_contents(__DIR__.'/local-admin.txt', "CMS URL:  http://127.0.0.1:8080/cms/\nEmail:    $email\nPassword: $password\n");
echo "Local superadmin ready; credentials written to .localdev/local-admin.txt\n";
