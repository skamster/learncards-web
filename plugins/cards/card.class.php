<?php
/**
 * The complete set of cardsets for a specified user!
 * @author skamster
 *
 */
class allCardSets {
	private $sets = array();
	private $dbPrefix;
	public function __construct($userid, $connection,$dbPrefix) {
		$GLOBALS["dbPrefix"] = $dbPrefix;
		$set = null;
		$setid = -1;
		$questionid = -1;
		$answerid = -1;
		foreach ($connection->query('SELECT * FROM '.$GLOBALS["dbPrefix"].'question_set WHERE ownerid="'.$userid.'"') as $setRow) {
			$set = new cardSet(s);
			$set -> setSetId($setRow['setid']);
			$setid = $setRow['setid'];
			$set -> setSetName($setRow['setname']);
			$set -> setSetDescription($setRow['setdescription']);
			foreach ($connection->query('SELECT * FROM '.$GLOBALS["dbPrefix"].'question_question WHERE '.$GLOBALS["dbPrefix"].'question_question.set="'.$setid.'";') as $questionRow) {
				$question = new question();
				$question -> setId($questionRow['questionid']);
				$question -> setQuestion($questionRow['question']);
				$question -> setMode($questionRow['mode']);
				$question -> setRightAnswered($questionRow['rightAnswered']);
				$question -> setWrongAnswered($questionRow['wrongAnswered']);
				$questionid = $questionRow['questionid'];
				foreach ($connection->query('SELECT * FROM '.$GLOBALS["dbPrefix"].'question_answer WHERE '.$GLOBALS["dbPrefix"].'question_answer.ownerquestion='.$questionid) as $answerRow) {
					$answerobj = new answer();
					$answerobj -> setAnswer($answerRow['answertext']);
					$answerobj -> setAnswerId($answerRow['answerid']);
					$question -> addAnswer($answerobj);
				}
				$set -> addQuestion($question);
			}
			array_push($this -> sets, $set);
		}
	}

	/**
	 * Get all the sets of cards
	 */
	public function getSets() {
		return $this -> sets;
	}

	/**
	 * Delete a specific set
	 * @param cardSet $set
	 * @param unknown_type $connection
	 */
	public function deleteSet($set, $connection) {
		//TODO remove from list
		$set -> deleteSet($connection);
	}

	/**
	 * Create a new set..
	 * @param cardSet $set
	 * @param int $userid
	 * @param PDO $connection
	 */
	public function newSet($set, $userid, $connection) {
		$connection -> exec("INSERT INTO ".$GLOBALS['dbPrefix']."question_set (`setname`,`setdescription`, `ownerid`,  `editcount`, `createtimestamp`, `firstowner`) VALUES ('" . $set -> getSetName() . "', '" . $set -> getSetDescription() . "', " . $userid . ", 1, '2009-00-00 00:00:00', " . $userid . ");");
		$set -> setSetId($connection -> lastInsertId());
		array_push($this -> sets, $set);
	}

	public function getSetBySetId($setId) {
		foreach ($this->sets as $set) {
			if ($set -> getSetId() == $setId) {
				return $set;
			}
		}
		return false;
	}

}

/**
 * A cardset is a stock of questions
 * It's like you've some papers and, on the backside a answer, on the front the question. This is the stock of these.
 * @author skamster
 *
 */
class cardSet {
	private $setid;
	private $setname;
	private $setdescription;
	private $questions = array();
	private $tags = array();

	/**
	 * set the id
	 * @param int $setid
	 * @return nothing
	 */
	public function setSetId($setid) {
		$this -> setid = $setid;
	}

	/**
	 * Set the name of the set
	 * @param String $setname
	 * @return nothing
	 */
	public function setSetName($setname) {
		$this -> setname = $setname;
	}

	/**
	 * Set the description of the set
	 * @param String $description
	 * @return nothing
	 */
	public function setSetDescription($description) {
		$this -> setdescription = $description;
	}

	/**
	 * Add a question to the set (but not to the database)
	 * @param Question $question
	 * @return nothing
	 */
	public function addQuestion($question) {
		array_push($this -> questions, $question);
	}

	/**
	 * getter for the set-id
	 * @return int id
	 */
	public function getSetId() {
		return $this -> setid;
	}

	/**
	 * getter for the set-description
	 * @return String description
	 */
	public function getSetDescription() {
		return $this -> setdescription;
	}

	/**
	 * getter for the set-name
	 * @return unknown_type
	 */
	public function getSetName() {
		return $this -> setname;
	}

	public function getQuestions() {
		return $this -> questions;
	}

	/**
	 * Create a new question
	 * @param question $question
	 * @param unknown_type $connection
	 */
	public function newQuestion($question, $connection) {
		$connection -> exec("INSERT INTO `".$GLOBALS['dbPrefix']."question_question` (`set`, `question`, `mode`) VALUES (" . $this -> setid . ", '" . $question -> getQuestion() . "', '" . $question -> getMode() . "')");
		$question -> setId($connection -> lastInsertId());
		array_push($this -> questions, $question);
	}

	public function getQuestionById($questionid) {
		foreach ($this->questions as $question) {
			if ($question -> getQuestionId() == $questionid) {
				return $question;
			}
		}
		return false;
	}

	public function deleteSet($connection) {
		foreach ($this->questions as $question) {
			$question -> deleteQuestion($connection);
		}
		$connection -> exec("DELETE FROM ".$GLOBALS['dbPrefix']."question_set WHERE `".$GLOBALS['dbPrefix']."question_set`.`setid` = " . $this -> setid);
	}

	public function updateSetDescription($description, $connection) {
		$this -> setdescription = $description;
		$connection -> exec("UPDATE `".$GLOBALS['dbPrefix']."question_set` SET `setdescription` = '" . $description . "' WHERE `".$GLOBALS['dbPrefix']."question_set`.`setid` =" . $this -> setid . ";");
	}

	public function updateSetName($name, $connection) {
		$this -> setname = $name;
		$connection -> exec("UPDATE `".$GLOBALS['dbPrefix']."question_set` SET `setname` = '" . $name . "' WHERE `".$GLOBALS['dbPrefix']."question_set`.`setid` =" . $this -> setid . ";");
	}

}

class question {
	private $questionId;
	private $question;
	private $answers = array();
	private $mode;
	private $rightAnswered;
	private $wrongAnswered;
	private static $TEXTMODE = 1;
	private static $SELECTMODE = 2;

	public function addAnswer($answer) {
		array_push($this -> answers, $answer);
	}

	public function setMode($mode) {
		$this -> mode = $mode;
	}

	public function setId($questionId) {
		$this -> questionId = $questionId;
	}

	public function setQuestion($question) {
		$this -> question = $question;
	}

	public function setRightAnswered($nr) {
		$this -> rightAnswered = $nr;
	}

	public function setWrongAnswered($nr) {
		$this -> wrongAnswered = $nr;
	}

	public function getRightAnswered() {
		return $this -> rightAnswered;
	}

	public function getWrongAnswered() {
		return $this -> wrongAnswered;
	}

	public function getMode() {
		return $this -> mode;
	}

	public function getAnswers() {
		return $this -> answers;
	}

	public function getQuestion() {
		return $this -> question;
	}

	public function getQuestionId() {
		return $this -> questionId;
	}

	public function updateQuestion($question, $connection) {
		$this -> question = $question;
		$connection -> exec("UPDATE `".$GLOBALS['dbPrefix']."question_question` SET `question` = '" . $question . "' WHERE `".$GLOBALS['dbPrefix']."question_question`.`questionid` =" . $this -> questionId . ";");
	}

	public function checkRightAnswer($answertext, $connection) {
		foreach ($this->answers as $answer) {
			// TODO build in option for diffrent answers!
			if ($answer -> getAnswer() == $answertext) {
				$this -> rightAnswered += 1;
				$connection -> exec("UPDATE `".$GLOBALS['dbPrefix']."question_question` SET `rightAnswered` = '" . $this -> rightAnswered . "' WHERE `".$GLOBALS['dbPrefix']."question_question`.`questionid` =" . $this -> questionId . " LIMIT 1 ;");
				return true;
			}
		}
		$this -> wrongAnswered += 1;
		$connection -> exec("UPDATE `".$GLOBALS['dbPrefix']."question_question` SET `wrongAnswered` = '" . $this -> wrongAnswered . "' WHERE `".$GLOBALS['dbPrefix']."question_question`.`questionid` =" . $this -> questionId . " LIMIT 1 ;");
		return false;
	}

	public function newAnswer($answer, $connection) {
		$connection -> exec("INSERT INTO `".$GLOBALS['dbPrefix']."question_answer` (`ownerquestion`, `answertext`) VALUES ('" . $this -> questionId . "', '" . $answer -> getAnswer() . "');");
		$answer -> setAnswerId($connection -> lastInsertId());
		array_push($this -> answers, $answer);
	}

	public function deleteQuestion($connection) {
		foreach ($this->answers as $answer) {
			$answer -> deleteAnswer($connection);
		}
		$connection -> exec("DELETE FROM `".$GLOBALS['dbPrefix']."question_question` WHERE `".$GLOBALS['dbPrefix']."question_question`.`questionid` = " . $this -> questionId);
	}

}

class answer {
	private $answerid;
	private $answer;

	public function getAnswerId() {
		return $this -> answerid;
	}

	public function getAnswer() {
		return $this -> answer;
	}

	public function setAnswerId($answerid) {
		$this -> answerid = $answerid;
	}

	public function setAnswer($answer) {
		$this -> answer = $answer;
	}

	public function deleteAnswer($connection) {
		$connection -> exec("DELETE FROM `".$GLOBALS['dbPrefix']."question_answer` WHERE `".$GLOBALS['dbPrefix']."question_answer`.`answerid` = " . $this -> answerid);
	}

}

class tag {
	private $tagid;
	private $tagtext;
	public function __construct($tagid, $tagtext) {
		$this -> tagid = $tagid;
		$this -> tagtext = $tagtext;
	}

	public function getTagId() {
		return $this -> tagid;
	}

	public function getTagText() {
		return $this -> tagtext;
	}

}

class cardtools {

	public static $TEXT = 0;
	public static $RADIO = 1;
	public static $MULTIPLE = 2;
	public static function oneBeforeInArray($array, $position) {
		if (count($array) > 1) {
			$beforePosition = ($position - 1);
			if ($beforePosition < 0) {
				return (count($array) - 1);
			} else {
				return $beforePosition;
			}
		} else {
			return 0;
		}
	}

	public static function randomArrayPosition($array) {
		if (count($array) > 1) {
			return rand(0, count($array) - 1);
		} else {
			return 0;
		}
	}

}
?>