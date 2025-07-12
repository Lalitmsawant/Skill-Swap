<?php
// api.php - Main API endpoint for the Skill Sharing Platform

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = 'localhost';
$dbname = 'skill_sharing_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get the action parameter
$action = $_GET['action'] ?? '';

// Route requests based on action
switch ($action) {
    case 'get_skills':
        getSkills($pdo);
        break;
    case 'get_profiles':
        getProfiles($pdo);
        break;
    case 'search_skills':
        searchSkills($pdo);
        break;
    case 'get_skill':
        getSkill($pdo);
        break;
    case 'create_skill':
        createSkill($pdo);
        break;
    case 'update_skill':
        updateSkill($pdo);
        break;
    case 'delete_skill':
        deleteSkill($pdo);
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

// Function to get all skills
function getSkills($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM skills ORDER BY created_at DESC");
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'skills' => $skills]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to fetch skills']);
    }
}

// Function to get all profiles
function getProfiles($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM profiles ORDER BY created_at DESC");
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get skills for each profile
        foreach ($profiles as &$profile) {
            $skillsStmt = $pdo->prepare("SELECT skill_name FROM profile_skills WHERE profile_id = ?");
            $skillsStmt->execute([$profile['id']]);
            $skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);
            $profile['skills'] = $skills;
        }
        
        echo json_encode(['success' => true, 'profiles' => $profiles]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to fetch profiles']);
    }
}

// Function to search skills
function searchSkills($pdo) {
    $query = $_GET['q'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE name LIKE ? OR category LIKE ? ORDER BY rating DESC");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'skills' => $skills]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to search skills']);
    }
}

// Function to get a single skill
function getSkill($pdo) {
    $id = $_GET['id'] ?? '';
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Skill ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
        $stmt->execute([$id]);
        $skill = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($skill) {
            echo json_encode(['success' => true, 'skill' => $skill]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Skill not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to fetch skill']);
    }
}

// Function to create a new skill
function createSkill($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = $input['name'] ?? '';
    $category = $input['category'] ?? '';
    $description = $input['description'] ?? '';
    $rating = $input['rating'] ?? 0;
    $reviews = $input['reviews'] ?? 0;
    
    if (!$name || !$category) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and category are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO skills (name, category, description, rating, reviews, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $category, $description, $rating, $reviews]);
        
        $skillId = $pdo->lastInsertId();
        
        echo json_encode(['success' => true, 'skill_id' => $skillId]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create skill']);
    }
}

// Function to update a skill
function updateSkill($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? '';
    $name = $input['name'] ?? '';
    $category = $input['category'] ?? '';
    $description = $input['description'] ?? '';
    $rating = $input['rating'] ?? 0;
    $reviews = $input['reviews'] ?? 0;
    
    if (!$id || !$name || !$category) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID, name, and category are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, description = ?, rating = ?, reviews = ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$name, $category, $description, $rating, $reviews, $id]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Skill not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update skill']);
    }
}

// Function to delete a skill
function deleteSkill($pdo) {
    $id = $_GET['id'] ?? '';
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Skill ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Skill not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to delete skill']);
    }
}
?>