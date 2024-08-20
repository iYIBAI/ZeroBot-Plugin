<?php

namespace App\Trait;

trait BaseModelTrait
{
    /**
     * 只查询指定字段的今日数据
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field 字段
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereToday($query, $field = 'created_at')
    {
        return $query->where($field, '>=', date('Y-m-d 00:00:00'));
    }

    /**
     * 只查询指定字段的昨日数据
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field 字段
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereYesterday($query, $field = 'created_at')
    {
        return $query->whereBetween($field, [
            date('Y-m-d 00:00:00', strtotime('-1 days')), date('Y-m-d 23:59:59', strtotime('-1 days'))
        ]);
    }

    /**
     * 简单快捷搜索
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $fields 字段 示例：['uid', ['mobile', 'like'], 'email', ['start_time', '>=', 'created_at'], ['start_time', '>=', 'created_at']]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $fields = [])
    {
        if(empty($fields)){
            return $query;
        }
        $request = request()->all();
        foreach ($fields as $field) {
            if(is_string($field) && isset($request[$field])){
                $query->where($field, $request[$field]);
            }elseif(is_array($field) && count($field) >= 2 && isset($request[$field[0]])){
                isset($field[2]) ?: $field[2] = $field[0];
                $value = $request[$field[0]];
                if($field[1] == 'like'){
                    $value = "%{$value}%";
                }
                $query->where($field[2], $field[1], $value);
            }
        }
        return $query;
    }
}
