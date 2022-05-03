<?php

/**
 * Databaseconnection
 */
class Model
{

	private $conn = null;

	function  __construct()
	{
		try {
			$this->conn = new PDO("mysql:host=" . SERVERNAME . ";dbname=" . DBNAME . ";charset=UTF8", USERNAME, PASSWORD);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get bookings
	 * @return array 
	 */
	public function get_bookings($event_id)
	{
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
	 * Get booking by name
	 * @return bool 
	 */
	public function get_booking_by_name($event_id, $name, $givenname)
	{
		$query = "SELECT * FROM bookings WHERE event_id = :event_id AND name = :name AND givenname = :givenname AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":givenname", $givenname, PDO::PARAM_STR);
		try {
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $result[0];
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get stornos
	 * @return array 
	 */
	public function get_stornos($event_id)
	{
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
	public function get_booking_count($event_id)
	{
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
	 * Change person count
	 */
	public function change_persons($event_id, $booking_id, $persons)
	{
		$query = "UPDATE bookings Set persons = :persons WHERE event_id = :event_id AND id = :booking_id AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		$stmt->bindValue(":booking_id", $booking_id, PDO::PARAM_INT);
		$stmt->bindValue(":persons", $persons, PDO::PARAM_INT);
		try {
			$stmt->execute();
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Does booking exists in DB
	 * @return bool 
	 */
	public function booking_exists($event_id, $name, $givenname)
	{
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
	public function new_booking($event_id, $event_name, $event_date, $name, $givenname, $email = null, $persons = 1)
	{
		$event_date = date("Y-m-d H:i:s", strtotime($event_date));
		$query = "INSERT INTO bookings (event_id, event_name, event_date, name, givenname, email, persons) VALUES (:event_id, :event_name, :event_date, :name, :givenname, :email, :persons)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":event_id", $event_id, PDO::PARAM_STR);
		$stmt->bindValue(":event_name", $event_name, PDO::PARAM_STR);
		$stmt->bindValue(":event_date", $event_date, PDO::PARAM_STR);
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
	public function delete_booking($booking_id, $event_id)
	{
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

	/**
	 * Get list of event IDs which are open for booking and in the next 6 month
	 */
	public function get_recent_bookable_events()
	{
		$query = "SELECT DISTINCT(event_id) FROM bookings WHERE event_date BETWEEN NOW() AND NOW()+INTERVAL 6 MONTH";
		$stmt = $this->conn->prepare($query);
		try {
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$values = array();
			foreach ($result as $event) {
				$values[] = $event['event_id'];
			}
			return $values;
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get all active polls from db
	 */
	public function get_polls($active = 1)
	{
		$query = "SELECT * FROM polls WHERE active = :active AND deleted_flag = 0 ORDER BY create_date";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":active", $active, PDO::PARAM_INT);
		try {
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get specific active poll from db
	 */
	public function get_poll($poll_id)
	{
		$query = "SELECT * FROM polls WHERE id = :poll_id AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":poll_id", $poll_id, PDO::PARAM_INT);
		try {
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Get specific poll results from db
	 */
	public function get_poll_results($poll_id)
	{
		$query = "SELECT * FROM poll_results WHERE poll_id = :poll_id AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":poll_id", $poll_id, PDO::PARAM_INT);
		try {
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Does vote already exist in DB?
	 * @return bool 
	 */
	public function vote_exists($poll_id, $name, $givenname)
	{
		$query = "SELECT COUNT(*) AS vote FROM poll_results WHERE poll_id = :poll_id AND name = :name AND givenname = :givenname AND deleted_flag = 0";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":poll_id", $poll_id, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":givenname", $givenname, PDO::PARAM_STR);
		try {
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($result[0]['vote'] > 0) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $ex) {
			echo "Connection failed: " . $ex->getMessage();
		}
	}

	/**
	 * Process vote for poll
	 */
	public function process_vote($poll_id, $vote, $name, $givenname, $email = null)
	{
		if (is_array($vote)) {
			foreach ($vote as $single_vote) {
				$this->vote($poll_id, $single_vote, $name, $givenname, $email);
			}
		} else {
			$this->vote($poll_id, $vote, $name, $givenname, $email);
		}
	}

	/**
	 * Vote for poll
	 */
	public function vote($poll_id, $vote, $name, $givenname, $email = null)
	{
		$query = "INSERT INTO poll_results (poll_id, vote, name, givenname, email) VALUES (:poll_id, :vote, :name, :givenname, :email)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":poll_id", $poll_id, PDO::PARAM_STR);
		$stmt->bindValue(":vote", $vote, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":givenname", $givenname, PDO::PARAM_STR);
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
	 * Create new poll
	 */
	public function create_poll($name, $description, $options, $multichoice = 0)
	{
		if (isset($multichoice)) {
			$multichoice = 1;
		} else {
			$multichoice = 0;
		}
		$query = "INSERT INTO polls (name, description, options, multichoice) VALUES (:name, :description, :options, :multichoice)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":description", $description, PDO::PARAM_STR);
		$stmt->bindValue(":options", $options, PDO::PARAM_STR);
		$stmt->bindValue(":multichoice", $multichoice, PDO::PARAM_INT);
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
	 * Set poll inactive
	 */
	public function change_poll_status($poll_id, $status)
	{
		$query = "UPDATE polls Set active = :status WHERE id = :poll_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(":poll_id", $poll_id, PDO::PARAM_INT);
		$stmt->bindValue(":status", $status, PDO::PARAM_INT);
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
