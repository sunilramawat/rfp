 <div  id="maintable">
  <div class="box-main-table" id="maintable" >
    <div class="table-responsive">
      <table class="table table-bordered admin-table dataTable"  id="example22" >
       
        <thead>
          <tr>
            <th>S.No</th>
            <th>Name</th> 
            <th>Email Address</th> 
            <th>Phone Number</th>
            <th>Address</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($Users as $no => $chip)
          <tr>
            <td>{{$no+1}}</td>
            <td>{{$chip->first_name.''.$chip->last_name}}</td>
            <td>{{$chip->email}}</td>
            <td>{{$chip->phone_code.' '.$chip->phone_number}}</td>
            <td>{{$chip->address}}</td>
            <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
              <a href="{{URL('admin/user/detail')}}/{{$chip->id}}">
                          <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>  
                   <!--      &nbsp;
              <a href="{{URL('admin/user/delete')}}/{{$chip->id}}">
                          <i class="fa fa-trash aria-hidden="true""></i>
                        </a>          --> 
            </td>
          </tr>
          @endforeach()
        </tbody>
      </table>
    </div>
  </div>
  <div class="box-main-bottom">
  <div class="box-main-showing">Showing {{$current_page}} to {{$row_count}} of {{$total_count}} entries</div>
  {{$Users->links() }}
  <!-- <ul class="pagination">
    <li class="page-item disabled">
      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item active" aria-current="page">
      <a class="page-link" href="#">2</a>
    </li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#">Next</a>
    </li>
  </ul> -->
  </div>  
</div>