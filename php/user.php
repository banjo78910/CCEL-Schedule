<?php
include("Result.php");
include("Excel_XML.php");
$database = "andysandbox";

class User {
    
    /* Notes:
     * - need to remove my pulvinar login info from the constructor and any other methods.
     */
    
    private $memberid;
    private $name;            // Stores the formatted name of the user.
    private $role;            // Stores role of the user (faculty, curator, or admin).
    private $resultArr;       // Stores articles that have the user's name listed as an author.
    private $connection;      // Stores a mysqli object to be used for interacting with the publications database.
    private $username;
    private $passwd;
    private $curatorSession;  // Stores a boolean value, indicating whether a curator or admin created this User object.
    
    /**
     * @param $memberid The ID number of a CTSC member. Should be retrieved from a cookie.
     */
    public function __construct($memberid) {
        $this->memberid = $memberid;
        $this->connection = new mysqli("pulvinar.case.edu", "asm115", "A5miis!");
        $nameResult = $this->connection->query("select * from {$GLOBALS["database"]}.User where memberid = " . $this->memberid . ";");
        $nameResult = $nameResult->fetch_array();
        $this->name = $nameResult["name"];
        $this->role = $nameResult["role"];
        $this->resultArr = array();
        /* Check if this User object was created while a curator/admin was logged in: */
        if (isset($_COOKIE["curatorsession"])) {
            $this->curatorSession = True;
        }
        else {
            $this->curatorSession = False;
        }
    }
    
    /**
     * Getter method for the user's name.
     * @return string The user's name.
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Getter method for the user's member ID.
     * @return int The user's CTSC membership ID.
     */
     public function getID() {
         return $this->memberid;
     }
     
     /**
      * Getter method for the user's role.
      * @return string The user's role.
      */
     public function getRole() {
         return $this->role;
     }
     
     /**
     * @return int The number of articles stored in the $resultArr.
     */
    public function numResults() {
        return count($this->resultArr);
    }
    
    /**
     * A method to determine how many of this User's publications cite the CTSC.
     * @return int The number of articles citing the CTSC listing this User's name as an author, excluding articles marked "not mine."
     */
    public function numCitations() {
        $excludeQuery = "select pmid from {$GLOBALS["database"]}.NotMine where memberid = $this->memberid";
        $results = $this->connection->query("select count(distinct pmid) 
                                             from {$GLOBALS["database"]}.Result natural join {$GLOBALS["database"]}.HasAuthor 
                                             where name = \"$this->name\" and ctsccited = 'yes' and pmid not in ($excludeQuery);");
        $results = $results->fetch_row();
        return $results[0];
    }
    
    /**
     * A method that retrieves articles containing an author whose name matches the name of the CTSC member,
     * excluding articles which have been marked as "not mine" by the member.
     * Articles are stored in this object's $resultArr to be used by other methods.
     */
    public function retrieveUnmarked($orderBy = "year desc") {
        /* Query that retrieves results this User has marked as "not mine": */
        $excludeQuery = "select pmid from {$GLOBALS["database"]}.NotMine where memberid = $this->memberid";
        $queryResult = $this->connection->query("select * from {$GLOBALS["database"]}.Result join {$GLOBALS["database"]}.HasAuthor using (pmid) where name = \"$this->name\" and pmid not in ($excludeQuery) order by $orderBy;");
        /* Store results in an array for use with other methods: */
        while (($result = $queryResult->fetch_assoc()) != Null) {
            array_push($this->resultArr, new Result($result));
        }
    }
    
    /**
     * A method that retrieves articles that the CTSC member has marked as "not mine."
     * Articles are stored in this object's $resultArr to be used by other methods.
     */
    public function retrieveMarked($orderBy = "year desc") {
        $str = "select distinct * from {$GLOBALS["database"]}.NotMine natural join {$GLOBALS["database"]}.Result where memberid = {$this->getID()} order by $orderBy;";
        $queryResult = $this->connection->query($str);
        if (!(isset($queryResult)) || $queryResult->num_rows == 0) {
            return;
        }
        else {
            while (($result = $queryResult->fetch_array()) != Null) {
                array_push($this->resultArr, new Result($result));
            }
        }
    }
    
    /**
     * A method to generate an html webpage consisting of results that might belong to the user.
     */
    public function presentResults() {
        $this->present();
    }
    
    /**
     * A method to generate an html webpage consisting of results the user has marked as "not mine."
     */
    public function presentMarked() {
        $this->present(True);
    }
    
    /**
     * A method to display a member's results, without any interaction functionality, for public viewing.
     */
    public function publicDisplay() {
        $shade = False; // Every other result will have a shaded background.
        /* Setup for pagination: */
        $i = 0;
        $resultsPerPage = 15;
        $this->genPageSelector($resultsPerPage);
        echo("<div class=\"page\" id=page1>");
        /* Generate divs to display each result: */
        foreach ($this->resultArr as $result) {
            if ($shade) {
                echo("<div class=\"result shaded\">");
            }
            else {
                echo("<div class=\"result\">");
            }
            $result->display();
            echo("<br><br>");
            echo("</div>");
            $i++; // Keeps track of how many results have been displayed, for pagination.
            /* If a page worth of results has been displayed, start a new "page" div: */
            if ($i % $resultsPerPage == 0) {
                echo("</div><div class=\"page\" id=page" . ($i / $resultsPerPage + 1) . ">");
            }
            $shade = !($shade); // Every other result will have a shaded background.
        }
        echo("</div>"); // End of div containing the very last page worth of results.
    }
    
    /**
     * A method to mark a result as not belonging to this user, and log the change in the Audit table.
     */
    public function markNotMine($pmid) {
        /* Mark the result: */
        $this->connection->query("insert into {$GLOBALS["database"]}.NotMine values($pmid, {$this->getID()});");
        $this->logChange("notmine", "pmid", $pmid);
    }
    
    /**
     * A method to restore a result to this user's listing, and log the restoration in the Audit table.
     */
    public function restoreResult($pmid) {
        /* Restore the result: */
        $this->connection->query("delete from {$GLOBALS["database"]}.NotMine where pmid = $pmid and memberid = {$this->getID()};");
        $this->logChange("restore", "pmid", $pmid);
    }
    
    /**
     * A method that restores all results the user has marked as "not mine" to the user's listing.
     */
    public function restoreAll() {
        $this->connection->query("delete from {$GLOBALS["database"]}.NotMine where memberid = {$this->getID()};");
        $this->logChange("restoreall");
    }
    
    /**
     * Export all of the user's publications as an Excel document.
     * Uses an API (Excel_XML.php).
     */
    public function excelExport() {
        $exporter = new Excel_XML();
        $table = array();
        foreach ($this->resultArr as $result) {
            array_push($table, $result->getArray());
        }
        $exporter->addArray($table);
        $exporter->generateXML();
    }
    
    /**
     * A method to generate an html webpage consisting of PubMed results.
     * $param $marked A parameter indicating whether the results being displayed have been marked as "not mine" or not.
     */
    private function present($marked = False) {
        $shade = False; // Every other result will have a shaded background.
        /* Setup for pagination: */
        $i = 0; // $i will keep track of how many results have been displayed.
        $resultsPerPage = 15;
        $this->genPageSelector($resultsPerPage);
        echo("<div class=\"page\" id=page1>");
        /* Generate divs to display each result: */
        foreach ($this->resultArr as $result) {
            /* Decide whether to shade the background of the div containing the current result: */
            if ($shade) {
                echo("<div class=\"result shaded\">");
            }
            else {
                echo("<div class=\"result\">");
            }
            $result->display(); // Display identifying info about the result.
            /* Display a button either for removing the result or restoring it: */
            if ($marked) {
                $buttonPrompt = " Restore this result to my listing.";
                $buttonText = "Restore";
            }
            else {
                $buttonPrompt =  " I am not an author of this publication.";
                $buttonText = "Remove";
            }
            /* Decide which jQuery script to use, based on whether results are being removed or restored: */
            if ($marked) {
                echo("<br><button class=\"markbutton\" onclick=\"restore({$result->getID()})\">$buttonText</button>$buttonPrompt");
            }
            else {
                echo("<br><button class=\"markbutton\" onclick=\"mark({$result->getID()})\">$buttonText</button>$buttonPrompt");
            }
            echo("<br><br>");
            echo("</div>"); // End of div containing the individual result.
            $i++; // Keeps track of how many results have been displayed, for pagination.
            /* If a page worth of results has been displayed, start a new "page" div: */
            if ($i % $resultsPerPage == 0) {
                echo("</div><div class=\"page\" id=page" . ($i / $resultsPerPage + 1) . ">");
            }
            $shade = !($shade); // Every other result will have a shaded background.
        }
        echo("</div>"); // End of div containing the very last page worth of results.
    }
    
    /**
     * A method to generate an html page selector for scrolling through a user's results using jQuery.
     * The page selector consists of numbered buttons corresponding to pages containing $resultsPerPage results each.
     * The method echoes a string embedded with all the necessary html to display as a page selector.
     */
    private function genPageSelector($resultsPerPage) {
        $numPages = ceil($this->numResults() / $resultsPerPage);
        $i = 1;
        echo("<br><div id=\"buttonholder\"><b>$numPages</b> pages total. ");
        while ($i <= $numPages) {
            echo("<div class=\"pagebutton\" id=\"pagebutton$i\">$i</div> ");
            $i++;
        }
        echo("<div style=\"float:right;\">
                  <form id=\"pagejumpform\">Jump to page: <input type=\"text\" size=\"3\" id=\"pagejump\" />
                  </form>
              </div>");
        echo("</div><br>");
    }
    
    /**
     * A method to insert a new tuple in the Audit log to record a modification.
     * @param $keyword Indicates whether the change was a removal or a restoration.
     * @param $description1 
     * @param $value1 
     * @param $description2 
     * @param $value2 
     */
    private function logChange($keyword, $description1 = '', $value1 = '', $description2 = '', $value2 = '') {
        /* Record the time of the modification: */
        $time = getdate();
        $formattedTime = "";
        $formattedTime .= $time["year"] . "-";
        $formattedTime .= $time["mon"] . "-";
        $formattedTime .= $time["mday"] . " ";
        $formattedTime .= $time["hours"] . ":";
        $formattedTime .= $time["minutes"] . ":";
        $formattedTime .= $time["seconds"];
        
        if ($this->curatorSession) {
            $description2 = "curatorID";
            $value2 = $_COOKIE["curatorsession"]; // The curator's memberID.
        }
        
        /* Insert a new tuple into the Audit table: */
        $this->connection->query("insert into {$GLOBALS["database"]}.Audit values('$formattedTime', '$keyword', '{$this->getID()}', '$description1', '$value1', '$description2', '$value2');");
    }
}
?>
