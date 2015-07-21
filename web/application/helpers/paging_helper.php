<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function paging($page,$rp,$total,$limit)
{
        $limit -= 1;

        $mid = floor($limit/2);

        if ($total>$rp)
            $numpages = ceil($total/$rp);
        else
            $numpages = 1;

        if ($page>$numpages) $page = $numpages;

            $npage = $page;

        while (($npage-1)>0&&$npage>($page-$mid)&&($npage>0))
            $npage -= 1;

        $lastpage = $npage + $limit;

        if ($lastpage>$numpages)
            {
            $npage = $numpages - $limit + 1;
            if ($npage<0) $npage = 1;
            $lastpage = $npage + $limit;
            if ($lastpage>$numpages) $lastpage = $numpages;
            }

        while (($lastpage-$npage)<$limit) $npage -= 1;

        if ($npage<1) $npage = 1;

        //echo $npage; exit;

        $paging['first'] = 1;
        if ($page>1) $paging['prev'] = $page - 1; else $paging['prev'] = 1;
        $paging['start'] = $npage;
        $paging['end'] = $lastpage;
        $paging['page'] = $page;
        if (($page+1)<$numpages) $paging['next'] = $page + 1; else $paging['next'] = $numpages;
        $paging['last'] = $numpages;
        $paging['total'] = $total;
        $paging['iend'] = $page * $rp;
        $paging['istart'] = ($page * $rp) - $rp + 1;

        if (($page * $rp)>$total) $paging['iend'] = $total;

        return $paging;
}

?>