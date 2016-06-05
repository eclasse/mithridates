<?php
/**
 * Content
 *
 */
class CContent
{
    
    private $db;
    /**
     * Constructor
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getAllContent()
    {
        $query = "SELECT *, (published <= NOW()) AS available FROM RM_Content;";
        $res = $this->db->ExecuteSelectQueryAndFetchAll($query);
        $html = "<ul>";
        
        foreach ($res as $result) {
            $status = (!$result->available ? 'inte ' : null) . 'publicerad';
            $html .= "<li> {$result->TYPE} ({$status})";
            $html .= $result->title . " (<a href='content_edit.php?id=" . $result->id . "'>redigera / </a><a href='content_delete.php?id=" . $result->id . "'>ta bort / </a> <a href='" . $this->getUrlToContent($result) ."'>visa</a>)" . "</li>";
        }
        $html .= "</ul>";
        
        return $html;
    }
    
    
    public function createContentDatabase()
    {
            $sql = "DROP TABLE IF EXISTS RM_Content;
            CREATE TABLE RM_Content
            (
                id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                slug CHAR(80) UNIQUE,
                url CHAR(80) UNIQUE,
                category VARCHAR(80) DEFAULT 'Nyhet',
                TYPE CHAR(80),
                title VARCHAR(80),
                DATA TEXT,
                FILTER CHAR(80),
                author VARCHAR(80),
                published DATETIME,
                created DATETIME,
                updated DATETIME,
                deleted DATETIME
            ) ENGINE INNODB CHARACTER SET utf8;
        ";
        
        return $this->db->ExecuteQuery($sql);
        
    }
    
    public function resetContentDatabase()
    {
        $sql = <<<EOD
            INSERT INTO RM_Content (slug, url, type, title, DATA, filter, published, created) VALUES
            ('blogpost-1', NULL, 'post', 'Välkommen till min blogg!', "Detta är en bloggpost.\n\nNär det finns länkar till andra webbplatser så kommer de länkarna att bli klickbara.\n\nhttp://dbwebb.se är ett exempel på en länk som blir klickbar.", 'clickable,nl2br', NOW(), NOW()),
            ('blogpost-2', NULL, 'post', 'Nu har sommaren kommit', "Detta är en bloggpost som berättar att sommaren har kommit, ett budskap som kräver en bloggpost.", 'nl2br', NOW(), NOW()),
            ('blogpost-3', NULL, 'post', 'Nu har hösten kommit', "Detta är en bloggpost som berättar att hösten har kommit, ett budskap som kräver en bloggpost", 'nl2br', NOW(), NOW());
        ;
EOD;
        return $this->db->ExecuteQuery($sql);
    }
    
    
    public function addContent($params)
    {
        $params[1] = $this->slugify($params[0]);
        
        
        $sql = '
            INSERT INTO RM_Content (title, slug, DATA, category, type, filter, author, published, created, updated)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL);
        ';
        $res = $this->db->ExecuteQuery($sql, $params);
        if ($res) 
        {
            $output = 'Innehåll sparat!';
        } 
        else 
        {
            $output = 'Innehåll sparades ej.<br><i>' . print_r($this->db->ErrorInfo(), 1) . '</i>';
        }
        return $output;
    }
    
    
    
    public function updateContent($params)
    {
        $sql = '
            UPDATE RM_Content SET
                title   = ?,
                slug    = ?,
                DATA    = ?,
                category = ?,
                filter  = ?,
                published = ?,
                updated = NOW()
            WHERE 
                id = ?
        ';
        
        //$params = array($title, $slug, $url, $data, $type, $filter, $published, $id);
        
        $res = $this->db->ExecuteQuery($sql, $params);
        if($res) {
          $output = 'Innehåll sparat!';
        }
        else {
          $output = 'Innehåll sparades ej.<br><i>' . print_r($this->db->ErrorInfo(), 1) . '</i>';
        }
    }
    
    public function selectContent($id)
    {
        $sql = 'SELECT * FROM RM_Content WHERE id = ?';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $id);
        return $res[0];
    }
    
    public function deleteContent($params)
    {
        $sql = 'DELETE FROM RM_Content WHERE id = ?'; 
        
        $res = $this->db->ExecuteQuery($sql, $params);
        if($res) {
          $output = 'Innehåll raderat!';
        }
        else {
          $output = 'Innehåll raderades ej.<br><i>' . print_r($this->db->ErrorInfo(), 1) . '</i>';
        }
        
        return $output;
    }
    
    
    function getUrlToContent($content) {
      switch($content->TYPE) {
        case 'page': return "content_page.php?url={$content->url}"; break;
        case 'post': return "content_blog.php?slug={$content->slug}"; break;
        default: return null; break;
      }
    }
    
    
    /**
     * Create a slug of a string, to be used as url.
     *
     * @param string $str the string to format as slug.
     * @returns str the formatted slug. 
     */
    function slugify($str) {
      $str = mb_strtolower(trim($str));
      $str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
      $str = preg_replace('/[^a-z0-9-]/', '-', $str);
      $str = trim(preg_replace('/-+/', '-', $str), '-');
      return $str;
    }
    
}
