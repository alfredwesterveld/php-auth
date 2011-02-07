<?php

/**
 * 
 * @author alfred
 *
 */
class Authentication {
    private $db;
    
    public function __construct(PDO $db, PasswordHash $hasher) {
        $this->db = $db;
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->hasher = $hasher;
    }
    
    public function createTables() {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS users " .
            "(id INTEGER PRIMARY KEY , username TEXT NOT NULL UNIQUE, " .
            "email TEXT NOT NULL, hash TEXT NOT NULL, active BOOLEAN)");
    }

    /**
     * Check that email is unique
     * @param <type> $email
     * @return <type>
     */
    public function checkEmailIsUnique($email) {
        $stmt = $this->db->prepare(
            "SELECT email FROM users WHERE email = :email");
        $stmt->execute(array(
            ":email" => $email
        ));
        $row = $stmt->fetch();
        return $row['email'] != $email;
    }

    public function checkUsernameIsUnique($username) {
        $stmt = $this->db->prepare(
            "SELECT username FROM users WHERE username = :username");
        $stmt->execute(array(
            ":username" => $username
        ));
        $row = $stmt->fetch();
        return $row['username'] != $username;
    }

    public function create($username, $email, $password) {
        $hash = $this->hasher->HashPassword($password);
        
        if (strlen($hash) < 20) {
            // @codeCoverageIgnoreStart
            throw new Exception('Failed to hash new password');
            // @codeCoverageIgnoreEnd
        }

        if (!$this->checkEmailIsUnique($email)) {
                return false;
        }

        $stmt = $this->db->prepare(
            "INSERT OR IGNORE INTO users (username, email, hash) " .
            "VALUES (:username, :email, :hash)");
        $stmt->execute(array(
            ":username" => $username,
            ":email" => $email,
            ":hash" => $hash
        ));
        return $stmt->rowCount() == 1;
    }
    
    public function login($credentials, $password) {
        $stmt = $this->db->prepare(
            "SELECT hash FROM users WHERE username = :credentials " .
            "OR email = :credentials");
        $stmt->execute(array(
            ":credentials" => $credentials
        ));
        $row = $stmt->fetch();
        if ($this->hasher->CheckPassword($password, $row['hash'])) {
            return true;
        } else {
            return false;
        }
    }
    
    public function isActivated($username) {
        $stmt = $this->db->prepare(
            "SELECT active FROM users WHERE username = :username");
        $stmt->execute(array(
            ":username" => $username
        ));
        $row = $stmt->fetch();
        return $row['active'] == true;
    }
    
    public function createEmailVerificationSecret($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
	        throw new EmailException();
        }

        $hash = $this->hasher->HashPassword($email);
        if (strlen($hash) < 20)
	        throw new Exception('Failed to hash new password');
        return $hash;
    }
    
    public function verifyEmail($credentials, $secret) {
        if ($this->hasher->CheckPassword($credentials, $secret)) {
            $placeholders = array(true, $credentials, $credentials);
            $stmt = $this->db->prepare(
                "UPDATE users SET active = :active WHERE " .
                "username = :credentials OR email = :credentials");
            $stmt->execute(array(
                ":active" => true,
                ":credentials" => $credentials
            ));
            return true;
	    } else {
	        return false;
	    }
    }
}

class EmailException extends Exception {}
