<?php

namespace App\Http\Controllers;

use App\Models\Fourniture;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\validationrequest;

class FournitureController extends Controller
{   
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $Kitchens = Fourniture::where('category','Kitchen')->get();
        
        foreach($Kitchens as $Kitchen){
            $images = Image::where('fourniture_id',$Kitchen->id)->get();
            $Kitchen->image = $images[0]->url;
           
        }
        
        $Fournitures = Fourniture::orderBy('price','DESC')->paginate(3);
        
        foreach($Fournitures as $Fourniture){
            $images = Image::where('fourniture_id',$Fourniture->id)->get();
            $Fourniture->image = $images[0]->url;
           
        }
        
        return view('index',
            [
                'Kitchens'  => $Kitchens ,
                'Fournitures' => $Fournitures,
                
            ]
        );
    }

    public function stockUpdate(Request $request){
        $item = Fourniture::where('id',$request->id)->get()->first();
        
        $op = $request->quantity;
        if($op[0] === "-"){
            $val = intval($item->quantity) - intval(substr($op,1));
            
        }elseif($op[0] === "+"){
            $val = intval($item->quantity) + intval(substr($op,1));
            
        }else{
            $val = intval($item->quantity) + intval($op);
        }

        if($val < 0){
            $val = 0;
        }
        Fourniture::where('id',$request->id)->update([
            'quantity' => $val
        ]);
        return redirect('/Dashboard');
    }


    public function search(Request $request){
        $Fournitures = Fourniture::searchName($request->name)
                        ->searchPrice($request->price)
                        ->searchCategory($request->category)
                        ->get();

        foreach($Fournitures as $Fourniture){
            $Image = Image::where('fourniture_id',$Fourniture->id)->first()->url;
            $Fourniture->image = $Image; 
        } 
        return view('fourniture.store',['SearchedFournitures'=>$Fournitures]);                
    }

    public function stockDisplay()
    {   $Fournitures = Fourniture::all();
        foreach($Fournitures as $Fourniture){
            $Image = Image::where('fourniture_id',$Fourniture->id)->first()->url;
            $Fourniture->image = $Image; 
        } 
         
        return view('Fourniture.store',
            ['Fournitures' => $Fournitures]
        );
    }    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Fourniture.addFourniture');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(request $request)
    {   
        $request->validate([
            'name' => 'required|unique:fournitures',
            'dimensions' =>'required',
            'category' => 'required',
            'description' => 'required',
            'small_description' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            
        ]);

        $Fourniture = Fourniture::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'dimensions' => $request->input('dimensions'),
            'quantity' => $request->input('quantity'),
            'small_description' => $request->input('small_description'),
            'price' => $request->input('price'),
            'description' => $request->input('description')
            
        ]);

        if($request->has('images')){
            $images = $request->file('images');
            foreach($images as $imageFile ){
                $image = new Image;

                $imageUrl = uniqid().'.'.$imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('storage/fourniture'),$imageUrl);
                
                $image->url = $imageUrl;
                $image->fourniture_id = $Fourniture->id;
                $image->save(); 
            }
        }
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Fourniture = Fourniture::find($id);
        $Images = Image::where('fourniture_id',$Fourniture->id)->get(); 
        return view('Fourniture.show')
            ->with([
            'Fourniture'=>$Fourniture,
            'Images' => $Images    
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
      $Fourniture = Fourniture::find($id) -> first();
      $Images = Image::where('fourniture_id',$Fourniture->id)->get();

      return view('/Fourniture/updateFourniture') -> with([
        'Fourniture'=> $Fourniture,
        'Images'  => $Images
        ]
        );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(request $request, $id)
    {
        
        $request->validate([
           
            'price' => 'integer',
            'quantity' => 'integer',
            
        ]);
       
        Fourniture::where('id',$id)-> update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'dimensions' => $request->dimensions,
            'quantity' => $request->quantity,
            'small_description' => $request->small_description,
            'description' => $request->description,
            
        ]);

        if($request->has('images')){
            $images = $request->file('images');
            foreach($images as $imageFile ){
                $image = new Image;

                $imageUrl = uniqid().'.'.$imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('storage/fourniture'),$imageUrl);
                
                $image->url = $imageUrl;
                $image->fourniture_id = $id;
                $image->save(); 
            }
        }

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        
        Fourniture::destroy($id);
        return redirect('/Dashboard');
    }

    public function dashboardData(){
        $Fournitures = Fourniture::all();
        $total = $Fournitures->count();
        $total_kitchen = Fourniture::where('category','Kitchen')->count();
        $total_room = Fourniture::where('category','Room')->count();
        $total_living = Fourniture::where('category','Living room')->count();
        $total_bath = Fourniture::where('category','Bathroom')->count();
        $total_exterior = Fourniture::where('category','Exterior')->count();
        $total_other = Fourniture::where('category','Other')->count();
        
        return view('dashboard',[
            "Fournitures" => $Fournitures,
            "total" => $total,
            "total_kitchen" => $total_kitchen,
            "total_room" => $total_room,
            "total_living" => $total_living,
            "total_bath" => $total_bath,
            "total_exterior" => $total_exterior,
            "total_other" => $total_other,
        ]);
    }

    
}
