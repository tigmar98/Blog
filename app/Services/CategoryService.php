<?php

namespace Blog\Services;

use Blog\Contracts\CategoryServiceInterface;
use Illuminate\Support\Facades\Auth;
use Blog\Category;


class CategoryService implements CategoryServiceInterface
{	
	public function __construct(Category $category){
		$this->category = $category;
	}

	public function allUserCategories(){
		return $this->category->where('userId', Auth::user()['id'])->get();
	}

	public function getCategory($id){
		return $this->category->where('id', $id)->first();
	}

	public function newCategory($categoryName){
		return $this->category->create([
				'category' => $categoryName,
				'userId' => Auth::user()['id'],
			]);
	}

	public function updateCategory($id, $category){
		return $this->category->where('id', $id)->update([
				'category' => $category,
			]);
	}
	
	public function deleteCategory($id){
		return $this->category->findOrFail($id)->delete();
	}


}
