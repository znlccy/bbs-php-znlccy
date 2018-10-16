<?php
namespace app\index\Controller;
class Fpage{
    private $page;//当前页
    private $pagenum;//总页数
    public function __construct($page,$pagenum){
        $this->page=$page;
        $this->pagenum=$pagenum;
    }
    //首页
    private function first(){
        if($this->page==1){
            @$html.='<span>1</span>';
        }else{
            @$html.='<a href="/?page=1">1...</a>';
        }
        return $html;
    }
    //上一页
    private function prev(){
        if($this->page==1){
            @$html.='<span>上一页</span>';
        }else{
            @$html.='<a href="/?page='.($this->page-1).'">上一页</a>';
        }
        return $html;
    }
    //下一页
    private function next(){
        if($this->page == $this->pagenum){
            @$html.='<span>下一页</span>';
        }else{
            @$html.='<a href="/?page='.($this->page+1).'">下一页</a>';
        }
        return $html;
    }
    //尾页
    private function last(){
        if($this->page==$this->pagenum){
            @$html.='<span>'.$this->pagenum.'</span>';
        }else{
            @$html.='<a href="/?page='.($this->pagenum).'">...'.$this->pagenum.'</a>';
        }
        return $html;
    }
    //当前页
    private function currentpage(){
        return '<spanc>第'.$this->page.'页</spanc>';
    }
    public function pagelist(){
        return array('first'=>$this->first(),'prev'=>$this->prev(),'aaa'=>$this->currentpage(),'next'=>$this->next(),'last'=>$this->last());
    }
}