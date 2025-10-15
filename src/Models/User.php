<?php
namespace MediBook\Models;

use MediBook\Database\Connection;
use PDO;

class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    public function authenticate($email, $password)
    {
        $query = "SELECT * FROM users WHERE email = :email AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }
    
    public function findById($id)
    {
        $query = "SELECT * FROM users WHERE id = :id AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function updateLastLogin($userId)
    {
        $query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    public function create($data)
    {
        $query = "INSERT INTO users (username, email, password, first_name, last_name, role) 
                  VALUES (:username, :email, :password, :first_name, :last_name, :role)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }
    
    public function checkLoginAttempts($email)
    {
        // Verificar si hay más de 5 intentos fallidos en los últimos 15 minutos
        $query = "SELECT COUNT(*) as attempts FROM login_attempts 
                  WHERE email = :email 
                  AND success = FALSE 
                  AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['attempts'] >= 5;
    }
    
    public function logLoginAttempt($email, $success = false)
    {
        $query = "INSERT INTO login_attempts (email, ip_address, user_agent, success) 
                  VALUES (:email, :ip_address, :user_agent, :success)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $stmt->bindParam(':success', $success, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    public function createUserSession($userId)
    {
        $sessionToken = bin2hex(random_bytes(32));
        
        $query = "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent) 
                  VALUES (:user_id, :session_token, :ip_address, :user_agent)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':session_token', $sessionToken);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        
        if ($stmt->execute()) {
            return $sessionToken;
        }
        
        return false;
    }
    
    public function endUserSession($sessionToken)
    {
        $query = "UPDATE user_sessions 
                  SET is_active = FALSE, logout_at = CURRENT_TIMESTAMP 
                  WHERE session_token = :session_token AND is_active = TRUE";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':session_token', $sessionToken);
        
        return $stmt->execute();
    }
    
    public function validateSession($sessionToken)
    {
        $query = "SELECT us.*, u.* FROM user_sessions us 
                  JOIN users u ON us.user_id = u.id 
                  WHERE us.session_token = :session_token 
                  AND us.is_active = TRUE 
                  AND u.status = 'active'
                  AND us.last_activity > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':session_token', $sessionToken);
        $stmt->execute();
        
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($session) {
            // Actualizar última actividad
            $updateQuery = "UPDATE user_sessions 
                           SET last_activity = CURRENT_TIMESTAMP 
                           WHERE session_token = :session_token";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':session_token', $sessionToken);
            $updateStmt->execute();
        }
        
        return $session;
    }
    
    public function createPasswordResetToken($email)
    {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Eliminar tokens anteriores para este email
        $deleteQuery = "DELETE FROM password_reset_tokens WHERE email = :email";
        $deleteStmt = $this->db->prepare($deleteQuery);
        $deleteStmt->bindParam(':email', $email);
        $deleteStmt->execute();
        
        // Insertar nuevo token
        $query = "INSERT INTO password_reset_tokens (email, token, expires_at) 
                  VALUES (:email, :token, :expires_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expiresAt);
        
        if ($stmt->execute()) {
            return [
                'token' => $token,
                'expires_at' => $expiresAt
            ];
        }
        
        return false;
    }
    
    public function validatePasswordResetToken($token)
    {
        $query = "SELECT * FROM password_reset_tokens 
                  WHERE token = :token 
                  AND used = FALSE 
                  AND expires_at > NOW()";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    public function resetPasswordWithToken($token, $newPassword)
    {
        // Verificar token válido
        $tokenQuery = "SELECT email FROM password_reset_tokens 
                       WHERE token = :token 
                       AND used = FALSE 
                       AND expires_at > NOW()";
        
        $tokenStmt = $this->db->prepare($tokenQuery);
        $tokenStmt->bindParam(':token', $token);
        $tokenStmt->execute();
        
        $tokenData = $tokenStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tokenData) {
            return false;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Actualizar contraseña del usuario
            $updateQuery = "UPDATE users 
                           SET password = :password, updated_at = CURRENT_TIMESTAMP 
                           WHERE email = :email AND status = 'active'";
            
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':password', password_hash($newPassword, PASSWORD_DEFAULT));
            $updateStmt->bindParam(':email', $tokenData['email']);
            $updateStmt->execute();
            
            // Marcar token como usado
            $markUsedQuery = "UPDATE password_reset_tokens 
                             SET used = TRUE, used_at = CURRENT_TIMESTAMP 
                             WHERE token = :token";
            
            $markUsedStmt = $this->db->prepare($markUsedQuery);
            $markUsedStmt->bindParam(':token', $token);
            $markUsedStmt->execute();
            
            // Invalidar todas las sesiones activas del usuario por seguridad
            $invalidateQuery = "UPDATE user_sessions 
                               SET is_active = FALSE, logout_at = CURRENT_TIMESTAMP 
                               WHERE user_id = (SELECT id FROM users WHERE email = :email)";
            
            $invalidateStmt = $this->db->prepare($invalidateQuery);
            $invalidateStmt->bindParam(':email', $tokenData['email']);
            $invalidateStmt->execute();
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function cleanupExpiredTokens()
    {
        $query = "DELETE FROM password_reset_tokens WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($query);
        return $stmt->execute();
    }
}