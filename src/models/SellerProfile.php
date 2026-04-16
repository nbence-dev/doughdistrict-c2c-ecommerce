<?php

class SellerProfile
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function create($user_id, $shop_name, $bio)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO seller_profiles (user_id, shop_name, bio) VALUES (?, ?, ?)");
            return $stmt->execute([$user_id, $shop_name, $bio]);
        } catch (PDOException $e) {
            // Handle duplicate shop name
            if ($e->getCode() == 23000) {
                return false; // Shop name already exists
            }
            throw $e; // Rethrow other exceptions
        }

    }

    // Every page load for a logged-in seller - gets their profile
    public function findByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM seller_profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    // Looking up a profile by its own PK - used when joining from products/orders
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM seller_profiles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $shop_name, $bio)
    {
        $stmt = $this->db->prepare("UPDATE seller_profiles SET shop_name = ?, bio = ? WHERE id = ?");
        return $stmt->execute([$shop_name, $bio, $id]);
    }


    // Stripe Connect OAuth callback - stores the connected account ID
    public function setStripeAccount($id, $stripe_account_id)
    {

    }

    // Called after Stripe confirms onboarding is done
    public function setStripeOnboardingComplete($id, $complete)
    {

    }

    public function nameExists($shop_name, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM seller_profiles WHERE shop_name = ? AND id != ?");
            $stmt->execute([$shop_name, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM seller_profiles WHERE shop_name = ?");
            $stmt->execute([$shop_name]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>