<?php
/**
 * Databaseconnection
 */
class Model {

	private $conn = null;

	function  __construct() {
		try {
			$this->conn = new PDO("mysql:host=" . SERVERNAME . ";dbname=" . DBNAME . ";charset=UTF8", USERNAME, PASSWORD);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get bookings
	 * @return array 
	 */
	public function get_bookings($event_id) {
		$query = "SELECT * FROM bookings WHERE event_id = :event_id AND deleted_flag = 0 ORDER BY name";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		try {
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get stornos
	 * @return array 
	 */
	public function get_stornos($event_id) {
		$query = "SELECT * FROM bookings WHERE event_id = :event_id AND deleted_flag = 1 ORDER BY name";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		try {
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get bookings count
	 * @return array 
	 */
	public function get_booking_count($event_id) {
		$query = "SELECT SUM(persons) AS persons_count FROM bookings WHERE event_id = :event_id AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		try {
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (isset($result[0]['persons_count'])) {
				return $result[0]['persons_count'];
			} else {
				return 0;
			}
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Does booking exists in DB
	 * @return bool 
	 */
	public function booking_exists($event_id, $name, $givenname) {
		$query = "SELECT COUNT(*) AS booking_count FROM bookings WHERE event_id = :event_id AND name = :name AND givenname = :givenname AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":givenname", $givenname, PDO::PARAM_STR);
		try {
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($result[0]['booking_count'] > 0) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Insert new booking in DB
	 * @param string $event_id
	 * @param string $name
	 * @param string $givenname
	 * @return True in case of success
	 */
	public function new_booking($event_id, $name, $givenname, $email = null, $plus_one = null) {
		$persons = 1;
		if ($plus_one) {
			$persons = 2;
		}
		$query = "INSERT INTO bookings (event_id, name, givenname, email, persons) VALUES (:event_id, :name, :givenname, :email, :persons)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":givenname", $givenname, PDO::PARAM_STR);
		$stmt->bindValue(":persons", $persons, PDO::PARAM_INT);
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
		try {
			if ($stmt->execute()) {
				return true;
			}
			return false;
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Delete booking in DB
	 * @param string $booking_id
	 * @param string $event_id
	 * @return True in case of success
	 */
	public function delete_booking($booking_id, $event_id) {
		$query = "UPDATE bookings Set deleted_flag = 1, update_date = NOW() WHERE id = :booking_id AND event_id = :event_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":booking_id", $booking_id, PDO::PARAM_INT);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		try {
			if ($stmt->execute()) {
				return true;
			}
			return false;
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}
}