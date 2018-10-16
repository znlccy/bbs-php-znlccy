<?php

namespace app\admin\model;

use think\Model;

class Competition extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_competitions';

    /**
     * 多对多关联论坛
     */
    public function forums()
    {
        return $this->belongsToMany('Forum', 'tb_competition_forums', 'forum_id', 'competition_id');
    }

    /**
     * 多对多关联新闻
     */
     public function news()
     {
         return $this->belongsToMany('News', 'tb_competition_news', 'news_id', 'competition_id');
     }

     /**
      * 多对多关联参赛团队
      */
      public function teams()
      {
          return $this->belongsToMany('Team', 'tb_competition_teams', 'team_id', 'competition_id');
      }

      /**
       * 多对多关联创业导师
       */
      public function tutors()
      {
          return $this->belongsToMany('Tutor', 'tb_competition_tutors', 'tutor_id', 'competition_id');
      }

      /**
       * 多对多管理服务商
       */
      public function sps()
      {
          return $this->belongsToMany('Sp', 'tb_competition_sps', 'sp_id', 'competition_id');
      }

      /**
       * 多对多关联投资机构
       */
      public function investors()
      {
          return $this->belongsToMany('Investor', 'tb_competition_investors', 'investor_id', 'competition_id');
      }

      /**
       * 多对多关联组织机构
       */
      public function organizers()
      {
          return $this->belongsToMany('Organizer', 'tb_competition_organizers', 'organizer_id', 'competition_id');
      }

      /**
       * 富文本标签转义
       */
      public function setRichTextAttr($value)
      {
          return htmlspecialchars($value);
      }
  
      public function getRichTextAttr($value)
      {
          return htmlspecialchars_decode($value);
      }

}
