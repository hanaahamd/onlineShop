<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\Redirect;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
//use Image;
//use Intervention\Image\Facades\Image;

//use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }
    public function create()
    {

        return view('admin.category.create');
    }
    public function store(Request $request)
    {


             $validator = Validator::make($request->all(),[
                'name' => 'required',
                'slug' => 'required|unique:categories',
                'status' => 'required|boolean',
            ]);


        if ($validator->passes()) {


            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;

            $category->save();

          // $oldImage = $category->image ?? null;


            //save image here
            if(!empty($request->image_id)){

           $tempImage = TempImage::find($request->image_id);
            $extArray = explode('.',$tempImage->name);
              $ext = last($extArray);

              $newImageName = $category->id.'-'.time().'.'.$ext;
              $sPath = public_path().'/temp/'. $tempImage->name;
              $dPath = public_path() . '/uploads/category/' . $newImageName;

              File::copy($sPath,$dPath);

                // Generate Image Thumbnail
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $manager = new ImageManager(new Driver());

                $image = $manager->read($sPath);
                 $image->cover(450,600);
                 $image->save($dPath);



                $category->image = $newImageName;
                $category->save();

            }
            session()->flash('success', 'Category added successfully');

            return response()->json([

                'status' => true,
                'message' => 'Category Added successfully'

            ]);
        } else {

            return response()->json([

                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($categoryId, Request $request) {

        $category = Category::find($categoryId);
        if(empty($category)){

            return redirect()->route('categories.index');
        }

      return view('admin.category.edit',compact('category'));



    }
    public function update($categoryId, Request $request) {

      $category = Category::find($categoryId);
      if(empty($category)){
       
     session()->flash('error','Category not found');

      return response()->json([
       'status' => false,
       'notFound'=>true,
       'message'=>'Category not found'

      ]);

      }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',

        ]);


        if ($validator->passes()) {


          // $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
           $category->showHome = $request->showHome;

            $category->save();

            $oldImage = $category->image ?? null;


            //save image here
            if (!empty($request->image_id)) {

                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id .'-'.time(). '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/'.$newImageName;

                File::copy($sPath, $dPath);

                // Generate Image Thumbnail
                $dPath = public_path() . '/uploads/category/thumb/'.$newImageName;
                $manager = new ImageManager(new Driver());

                $image = $manager->read($sPath);
                $image->cover(450, 600);
                $image->save($dPath);



                $category->image = $newImageName;
                $category->save();
            }
                  session()->flash('success', 'Category Updated successfully');

            return response()->json([

                'status' => true,
                'message' => 'Category Updated successfully'
               
            ]);
               } else {

              return response()->json([

                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
     
    

                 // Delete Old Images Here

            //    File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
            //     File::delete(public_path() . '/uploads/category/'.$oldImage);
                 //if (!empty($oldImage)) {
                   // File::delete(public_path('/uploads/category/thumb/' . $oldImage));
                // }


              }
            
        
   
   
        public function destroy($categoryId, Request $request) {

        $category = Category::find($categoryId);
        
           if (empty($category)){
            session()->flash('error', 'Category not found');

            // return redirect()->route('categories.index');
            return response()->json([
                'status' => true,
                'message' => 'Category not found'
            ]);
            
           }
        File::delete(public_path() . '/uploads/category/thumb/' . $category->image);
        File::delete(public_path() . '/uploads/category/' . $category->image);
        
         $category->delete();
         
         session()->flash('success', 'Category deleted successfully');

        return response()->json([

            'status' => true,
           'message' => 'Category deleted successfully'
        ]);
    
    }
           
    
    
    
    }  
            

   