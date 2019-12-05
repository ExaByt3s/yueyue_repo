<?
class PageBar
{
    var $total;    
    var $onepage;
    var $num =10;            
    var $pagecount;
    var $total_page;
    var $offset;    
    var $linkhead;    
    
    function PageBar($total, $onepage, $form_vars='')
    {
        $pagecount = $_GET['pagecount'];
        $this->total      = $total;
        $this->onepage    = $onepage;
        $this->total_page = ceil($total/$onepage);

        if (empty($pagecount))
        {
            $this->pagecount = 1;
            $this->offset	 = 0;    
        }
        else
        {
            $this->pagecount = $pagecount;
            $this->offset    = ($pagecount-1)*$onepage;
        }
        
        if (!empty($form_vars))
        {
            $vars = explode("|", $form_vars);
            $chk = $vars[0];
            $chk_value = $_POST[$chk];

            if (empty($chk_value))
            {
                $formlink = "";
            }
            else
            {
				//print_r($vars);
                for ($i=0; $i<count($vars); $i++)
                {
                    $var     = $vars[$i];
                    $value   = $_POST[$var];
                    $addchar = $var."=".$value;
                    $addstr  = $addstr.$addchar."&";
                }

                $formlink = $addstr;//"&".substr($addstr, 0, strlen($addstr)-1);
            }

        }
        else
        {
            $formlink = "";
        }

        $linkarr = explode("pagecount=", $_SERVER['QUERY_STRING']);
        $linkft  = $linkarr[0];

        if (empty($linkft))
        {
            $this->linkhead = $_SERVER['PHP_SELF']."?".$formlink."&";
        }
        else
        {
            $this->linkhead = $_SERVER['PHP_SELF']."?".$linkft."&".$formlink;
        }

    }
    #End function PageBar();

    function offset()
    {
        return $this->offset;
    }
    #End function offset();

    function pre_page($char='')
    {
        $linkhead  = $this->linkhead;
        $pagecount = $this->pagecount;
        if (empty($char))
        {
            $char = "[<]";
        }

        if ($pagecount>1)
        {
            $pre_page = $pagecount - 1;
            return "<a href=\"$linkhead"."pagecount=$pre_page\">$char</a>";
        }
        else
        {
            return "<a href=\"$linkhead"."pagecount=1\">$char</a>";
        }

    }
    #End function pre_page();

    function next_page($char='')
    {
        $linkhead   = $this->linkhead;
        $total_page = $this->total_page;
        $pagecount  = $this->pagecount;
        if (empty($char))
        {
            $char = "[>]";
        }
        if ($pagecount<$total_page)
        {
            $next_page = $pagecount + 1;
            return "<a href=\"$linkhead"."pagecount=$next_page\">$char</a>";
        }
        else
        {
            return "<a href=\"$linkhead"."pagecount=$next_page\">$char</a>";
        }
    }
    #End function next_page();

    function num_bar($num='', $color='', $left='', $right='')
    {
        $num       = (empty($num))?10:$num;
        $this->num = $num;
        $mid       = floor($num/2);
        $last      = $num - 1; 
        $pagecount = $this->pagecount;
        $totalpage = $this->total_page;
        $linkhead  = $this->linkhead;
        $left      = (empty($left))?"[":$left;
        $right     = (empty($right))?"]":$right;
        $color     = (empty($color))?"#ff0000":$color;
        $minpage   = (($pagecount-$mid)<1)?1:($pagecount-$mid);
        $maxpage   = $minpage + $last;
        if ($maxpage>$totalpage)
        {
            $maxpage = $totalpage;
            $minpage = $maxpage - $last;
            $minpage = ($minpage<1)?1:$minpage;
        }

        for ($i=$minpage; $i<=$maxpage; $i++)
        {
            $char = $left.$i.$right;
            if ($i==$pagecount)
            {
                $char = "<font color='$color'>$char</font>";
            }

            $linkchar = "<a href='$linkhead"."pagecount=$i'>".$char."</a>";
            $linkbar  = $linkbar.$linkchar;
        }

        return $linkbar;
    }
    #End function num_bar();

    function pre_group($char='')
    {
        $pagecount   = $this->pagecount;
        $linkhead    = $this->linkhead;
        $num      	 = $this->num;
        $mid         = floor($num/2);
        $minpage     = (($pagecount-$mid)<1)?1:($pagecount-$mid);
        $char        = (empty($char))?"[<<]":$char;
        $pgpagecount = ($minpage>$num)?$minpage-$mid:1;
        return "<a href='$linkhead"."pagecount=$pgpagecount'>".$char."</a>";
    }
    #End function pre_group();

    function next_group($char='')
    {
        $pagecount = $this->pagecount;
        $linkhead  = $this->linkhead;
        $totalpage = $this->total_page;
        $num       = $this->num;
        $mid       = floor($num/2);
        $last      = $num; 
        $minpage   = (($pagecount-$mid)<1)?1:($pagecount-$mid);
        $maxpage   = $minpage + $last;
        if ($maxpage>$totalpage)
        {
            $maxpage = $totalpage;
            $minpage = $maxpage - $last;
            $minpage = ($minpage<1)?1:$minpage;
        }

        $char = (empty($char))?"[>>]":$char;
        $ngpagecount = ($totalpage>$maxpage+$last)?$maxpage + $mid:$totalpage;
        return "<a href='$linkhead"."pagecount=$ngpagecount'>".$char."</a>";
    }
    #End function next_group();

    function whole_num_bar($num='', $color='')
    {
        $num_bar    = $this->num_bar($num, $color);
        $pre_group  = $this->pre_group();
        $pre_page   = $this->pre_page();
        $next_page  = $this->next_page();
        $next_group = $this->next_group();

        return  $pre_group.$pre_page.$num_bar.$next_page.$next_group;
    }
    #End function whole_bar();
}
?>