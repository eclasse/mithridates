<?php
/**
 * Blog
 *
 */
class CBlog
{
    
    private $db;
    private $acronym;
    private $blogTitle;
    private $parameters;
    
    public function __construct($db, $acronym=null)
    {
        $this->db = $db;
        $this->acronym = $acronym;
        $this->blogTitle = null;
        $this->parameters = $this->createDefaultParameters();
        
    }
    
    public function getBlogPostsFromSlug($parameters, $textFilter)
    {
        $sqlParameters = array();
        $this->parameters = array_merge($this->parameters, $parameters);
        
        $sql = "SELECT * FROM RM_Content WHERE type = 'post'";
        
        if ($this->parameters['slug'] != null) {
            $sql .= ' AND slug = ?';
            $sqlParameters[] = $this->parameters['slug'];
        }
        
        if($this->parameters['category']) {
          $sql .= ' AND category = ?';
          $sqlParameters[] = $this->parameters['category'];
        }
        
        if(!$this->isAdmin())
        {
            $sql .= ' AND published <= NOW()';
        }
        
        $sql .= ' ORDER BY UNIX_TIMESTAMP(GREATEST(COALESCE(published, 0), COALESCE(updated, 0), COALESCE(created, 0))) DESC';
        
        if($this->parameters['hits']) {
            $sql .= ' LIMIT ' . $this->parameters['hits'];
        }
        
        
        try {
                
            $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $sqlParameters);
                
            if(isset($res[0]))
            {
                $html = $this->createBlogPosts($this->parameters, $res, $textFilter);
            }
            else 
            {
                if ($this->parameters['slug']) 
                {
                    throw new UnexpectedValueException('Det fanns inte en sådan bloggpost!'); //Lånad lösning
                } 
                else 
                {
                    throw new UnexpectedValueException('Det fanns inga bloggposter!'); //Lånad lösning
                }
            }
        } 
        catch (UnexpectedValueException $exception) {
        
            $html = "<p>" . $exception->getMessage() . "</p>";
        
        }
        
        return $html;
    }

    
    private function createBlogPosts($parameters, $res, $textFilter)
    {
        
        if ($parameters['slug'] != null) 
        {
            $html = $this->createBlogPost($parameters['slug'], $res, $textFilter);
        }
        else
        {     
            $html = "<div class='blogContainer'>";
            foreach($res as $blog) {
                $title  = htmlentities($blog->title, null, 'UTF-8');
                $author = htmlentities($blog->author, null, 'UTF-8');
                
                if (empty($author)) 
                {
                    $author = "John Doe";
                }
                
                $published = htmlentities($blog->published, null, 'UTF-8');
                $data   = $this->truncate($blog->DATA);
                            
                $editLink = $this->isAdmin() ? "<a href='content_edit.php?id={$blog->id}'>Uppdatera posten</a>" : null;
                $deleteLink = $this->isAdmin() ? "<a href='content_delete.php?id={$blog->id}'>Radera posten</a>" : null;
                $html .= <<<EOD
                    <article class='blogList'>
                        <header>
                            <h3><a href='content_blog.php?slug={$blog->slug}'>{$title}</a></h3>
                            <p class="font-small-italic">{$published} Av {$author}</p>
                            <p>{$data}</p>
                        </header>
                        <a href='content_blog.php?slug={$blog->slug}'>Läs mer -></a>
                        <footer>
                            <p>{$editLink} \t {$deleteLink}<p>
                        </footer>
                    </article>
EOD;
            }
            $html .= '</div>';
            $createLink = $this->isAdmin() ? "<a href='content_create.php'>Skapa ny post!</a>" : null;
            $html .= $createLink;
        }
        return $html;
    }
    
    private function createBlogPost($slug, $res, $textFilter)
    {
        $html = "<div class='blogList'>";
        foreach($res as $blog) {
            
            $title  = htmlentities($blog->title, null, 'UTF-8');
            $author = htmlentities($blog->author, null, 'UTF-8');
            
            if (empty($author)) 
            {
                $author = "John Doe";
            }
            
            $published = htmlentities($blog->published, null, 'UTF-8');
            $data   = $textFilter->doFilter(htmlentities($blog->DATA, null, 'UTF-8'), $blog->FILTER);
            
            $this->blogTitle = $title;
                
            $editLink = $this->isAdmin() ? "<a href='content_edit.php?id={$blog->id}'>Uppdatera posten</a>" : null;
            $deleteLink = $this->isAdmin() ? "<a href='content_delete.php?id={$blog->id}'>Radera posten</a>" : null;
            $html .= <<<EOD
                <article class='blogContainer'>
                    <header>
                        <h3><a href='content_blog.php?slug={$blog->slug}'>{$title}</a></h3>
                    </header>
                    <p class="font-small-italic">Av {$author}</p>
                    <p class="font-small-italic">{$published}</p>
                    <p>{$data}</p>
                    <footer>
                        <p>{$editLink} \t {$deleteLink}<p>
                    </footer>
                </article>
EOD;
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    public function getBlogTitle()
    {
        return $this->blogTitle;
    }
    
    private function createDefaultParameters()
    {
        $default = array (
            'slug' => null,
            'hits' => null,
            'category' => null,
        );
        
        return $default;
    }
    
    public function getCategoryLinks()
    {
        $html = "<a style='margin-right:5px' href='?'>Alla</a>";
        $sql = 'SELECT DISTINCT category FROM RM_Content;';
        $parameters = array();
        
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $parameters);
        
        foreach($res as $category) {
            $html .= "<a style='margin-right:5px;' href='?category=" . $category->category . "'>" . $category->category . "</a>";
        }
        
        return $html;
        
    }
    
    private function isAdmin()
    {
        $isAdmin = false;
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;
        if (isset($acronym)) {
            if (strcmp ($acronym , 'admin') === 0) {
                $isAdmin = true;
            }
        }
        return $isAdmin;
    }
    
    private function truncate($data)
    {
       $trunc = substr($data, 0, 30) . "...";
       return $trunc;
    }
    
    
    
    
}
