<?php
namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract{

    protected $availableIncludes=['user','product'];

    public function transform(Comment $comment){

        return [
            'id'=>$comment->id,
            'content'=>$comment->content,
            'grade'=>$comment->grade,
            'created_at'=>$comment->created_at,
            'status'=>$comment->status,
        ];
    }

    public function includeUser(Comment $comment){
        return $this->item($comment->user,new UserTransformer());
    }

    public function includeProduct(Comment $comment){
        return $this->item($comment->product,new ProductTransformer());
    }

    
}
 