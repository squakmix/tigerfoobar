<?php
class Data_model extends CI_Model {

	//called when constructed
	public function __construct() {
		$this->load->database();
	}

	//get all tag types
	public function testDB() {
		$query = $this->db->get('User');
		return $query->result_array();		
	}

	// ------------- METHODS FOR GETTING THE SCORE INFORMATION -------------
	public function getClaimScores($claimID) {
		$sql = "SELECT *, COUNT(ClaimID) AS noRatings, 
					(SELECT COUNT(ClaimID) 
					FROM Rating 
					WHERE ClaimID = $claimID) as Total
				FROM Rating
				WHERE ClaimID = $claimID
				GROUP BY Value
				ORDER BY Value";
		return $this->db->query($sql)->result_array();
	}

	// ------------- METHODS FOR CLAIM VIEW -------------
	public function getClaim($claimID) {
		$sql = "SELECT cl.ClaimID, cl.Link, cl.Title AS ClaimTitle, cl.Description, cl.Score AS ClaimScore, cl.UserID, cl.CompanyID, cl.Time AS ClaimTime, co.Name AS CoName, co.Rating AS CoScore, u.Name AS UserName
				FROM Claim cl
				LEFT JOIN Company co
				ON cl.CompanyID = co.CompanyID
				LEFT JOIN User u
				ON cl.UserID = u.UserID
				WHERE cl.ClaimID = $claimID";
		return $this->db->query($sql)->result_array();
	}
	
	public function getClaimTags($claimID) {
		$sql = "SELECT DISTINCT t.Name, COUNT(ct.Claim_ClaimID) as votes
				FROM Tags t
				LEFT JOIN Claim_has_Tags ct
				ON t.TagsID = Tags_TagsID
				LEFT JOIN Claim c
				ON c.ClaimID = Claim_ClaimID
				WHERE t.Type = 'Claim Tag'
				AND c.ClaimID = $claimID
				GROUP BY t.Name";
		return $this->db->query($sql)->result_array();
	}

	// Need to get number of ratings for each claim
	
	// ------------- METHODS FOR COMPANY VIEW -------------	
	public function getCompany($companyID) {
		$sql = "SELECT *
				FROM Company
				WHERE CompanyID = $companyID";
		return $this->db->query($sql)->result_array();
	}

	public function getCompanyClaims($companyID) {
		$sql = "SELECT cl.*, cl.numScores AS noRatings, co.numClaims AS Total, co.Name
				FROM Claim cl
				LEFT JOIN Company co
				ON co.CompanyID = cl.CompanyID
				WHERE co.CompanyID = $companyID
				GROUP BY cl.Score";
		return $this->db->query($sql)->result_array();
	}
	
	public function getCompanyTags($companyID) {
		$sql = "SELECT DISTINCT t.Name, COUNT(c.Company_CompanyID) as votes
				FROM Tags t
				LEFT JOIN Company_has_Tags c
				ON c.Tags_TagsID = t.TagsID
				WHERE t.Type = 'Industry'
				AND c.Company_CompanyID = $companyID
				GROUP BY t.Name";
		return $this->db->query($sql)->result_array();
	}
	
	//------------- METHODS FOR TREEMAP VIEW ------------
	
	public function getTopCompaniesWithClaims() {
		$N = 10;
		$M = 10;
		
		$sql = "Select cl.ClaimID as ClaimID, cl.Title, cl.Score, cl.numScores, topCompanies.numClaims, topCompanies.Name, topCompanies.Rating 
			From Claim cl
			Join
				(Select * 
				from Company co
				GROUP BY co.CompanyID
				Order by co.numClaims DESC
				Limit 0, $N) topCompanies
			On cl.companyID = topCompanies.companyID";
		return $this->db->query($sql)->result_array();
		/*TODO: Make sure this is returning the correct data; something's funky with scores
		*/
	}
	
	//Gets data for the top companies along with their claims and formats them as JSON to be used in a treemap view
	public function getTopCompaniesWithClaimsJSON() {

		$topCompanies = $this->getTopCompaniesWithClaims();
		$jsonDataObj = '{"name": "Top companies with claims", "children": [';
		
		//Builds JSON out of the data in the $data array
		$companiesWithClaims = '';
		$currCompany = "";
		
		for ($i = 0; $i < count($topCompanies); $i++) {
		
			
			//foreach($topClaims as $topClaim) {

			if ($topCompanies[$i]["Name"] != $currCompany) {
				$currCompany = $topCompanies[$i]["Name"];
				$companiesWithClaims .= '{"name": "' . $topCompanies[$i]["Name"] . '", "children": [';
			}
			
			$claims = '';
			$rating = $topCompanies[$i]["Rating"];			
			while (($i < count($topCompanies)) && $topCompanies[$i]["Name"] == $currCompany) {
				$title = str_replace("'","", $topCompanies[$i]["Title"]);
				$size = str_replace("'","", $topCompanies[$i]["numScores"]);
				$score = $topCompanies[$i]["Score"];
				$claimID = $topCompanies[$i]["ClaimID"];
				
				
				$claims .= '{"name" : "' . $title . '", "claimID" : "' . $claimID . '", "score" : "' . $score .'", "size" : ' . $size . '},';
				$i++;
			} 
			$claims = rtrim($claims, ",");
			
			$companiesWithClaims .= $claims;
			$companiesWithClaims .= '], "rating": "' . $rating . '"},';
			
			$i--;
		}
		
		$companiesWithClaims = rtrim($companiesWithClaims, ",");
		$jsonDataObj .= $companiesWithClaims . ']}';
		
		return $jsonDataObj;
		
		/* TODO: Clean up the ugly fencepost shit going on here
		*/
	}
	
	//Gets data for the top companies that have the given tag
	public function getClaimsForCompanyJSON($companyID) {
		$topClaimsForCompany = $this->getCompanyClaims($companyID);
		$companyName = $topClaimsForCompany[0]["Name"];
		$jsonDataObj = '{"name": "Top claims for $companyName", "children": [';
		
		//Builds JSON out of the data in the $data array
		$companiesWithClaims = '';
		$claims = "";
		
		for ($i = 0; $i < count($topClaimsForCompany); $i++) {
				$title = str_replace("'","", $topClaimsForCompany[$i]["Title"]);
				$claimID = $topClaimsForCompany[$i]["ClaimID"];
				$score = $topClaimsForCompany[$i]["Score"];
				$size = str_replace("'","", $topClaimsForCompany[$i]["numScores"]);	
				
				$claims .= '{"name" : "' . $title . '", "claimID" : "' . $claimID . '", "score" : "' . $score .'", "size" : ' . $size . '},';

		}
		
		$claims = rtrim($claims, ",");
		$jsonDataObj .= $claims. ']}';
		
		return $jsonDataObj;
	}
	
	// ------------- METHODS FOR TAG VIEW ---------------
	public function getTags($tagID) {
		$sql = "SELECT DISTINCT t.Name, ct.Claim_ClaimID, c.Title, c.Score AS ClScore, co.CompanyID, co.Name AS CoName, co.Rating AS CoScore
				FROM Tags t
				LEFT JOIN Claim_has_Tags ct
				ON t.TagsID = ct.Tags_TagsID
				LEFT JOIN Claim c
				ON ct.Claim_ClaimID = c.ClaimID
                LEFT JOIN Company co
                ON c.CompanyID = co.CompanyID
				WHERE t.tagsID = $tagID";
		return $this->db->query($sql)->result_array();
	}	

	// ------------- METHODS FOR DISCUSSION VIEW --------
	public function getDiscussion($claimID) {
		$sql = "SELECT d.Comment, d.UserID, u.Name, d.votes, d.level, d.Time
				FROM Discussion d
				LEFT JOIN User u
				ON u.UserID = d.UserID
				WHERE d.ClaimID = $claimID";
		return $this->db->query($sql)->result_array();
	}	
	
	// ------------- METHODS FOR USER VIEW ---------------
	public function getUser($userID) {
		$query = $this->db->get_where('User', array('UserID' => $userID));
		return $query->row();
	}
	
	public function getUserClaims($userID) {
		$sql = "SELECT c.ClaimID, c.Title, r.Value
				FROM User u
				LEFT JOIN Claim c
				ON c.UserID = u.UserID
				LEFT JOIN Rating r
				ON c.ClaimID = r.ClaimID
				WHERE u.UserID = $userID";
		return $this->db->query($sql)->result_array();
	}	
	
	public function getUserComments($userID) {
		$sql = "SELECT d.ClaimID, d.Comment, d.votes, d.Time, r.Value, c.Title
				FROM User u
				LEFT JOIN Discussion d
				ON u.UserID = d.UserID
				LEFT JOIN Rating r
				ON r.ClaimID = d.ClaimID
				LEFT JOIN Claim c
				ON r.ClaimID = c.ClaimID
				WHERE u.UserID = $userID";
		return $this->db->query($sql)->result_array();
	}	
	
	public function getUserVotes($userID) {
		$sql = "SELECT u.Name, v.Value, v.CommentID, d.Comment, v.Time, c.ClaimID, c.Title, co.CompanyID, co.Name AS CoName
				FROM User u
				LEFT JOIN Vote v
				ON u.UserID = v.UserID
				LEFT JOIN Discussion d
				ON v.CommentID = d.CommentID
				LEFT JOIN Claim c
				ON c.ClaimID = d.ClaimID
				LEFT JOIN Company co
				ON co.CompanyID = c.CompanyID
				WHERE u.UserID = $userID";
		return $this->db->query($sql)->result_array();
	}	
}