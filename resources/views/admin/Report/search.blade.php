<div  id="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
              <table class="table table-bordered admin-table dataTable"  id="example" >
               
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Name</th> 
                    <th>Sub Categories</th> 
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Trades_manage as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->trade}}</td>
                    <td>
                         <a href="{{URL('admin/subtrade/view')}}/{{$chip->id}}" class="{{ (request()->is('admin/trade*')) ? 'nav-link active' : 'nav-link' }}">
                        {{$chip->SubTradeManage->count()}}
                      </a>
                    </td>
                   <!--  <td>{{$chip->block_status}} </td> -->
                    <td>
                      <!--  <label class="switch">
                        <input type="checkbox" checked>
                        <span class="switchslider round"></span>
                      </label>  -->
                                @if($chip->block_status == 1)
                                   <label class="switch ">
                                  <a onClick="ChangeStatus({{$chip->id}},0)" class="toggle-btn " style="cursor: pointer">
                                    <!-- <i class="fa fa-check-circle enablecheck fa-lg"></i> -->
                                      <input type="checkbox" checked>
                                       <span class="switchslider round"></span>
                                  </a>
                                  </label>  
                                @else
                                   <label class="switch">
                                  <a onClick="ChangeStatus({{$chip->id}},1)" class="toggle-btn" style="cursor: pointer">
                                    <!-- <i class="fa fa-times-circle disblecheck fa-lg"></i> -->
                                    <!-- <i class="fa fa-toggle-off "></i> -->
                                    <input type="checkbox" >
                                       <span class="switchslider round"></span>
                                  </a>  
                                </label>
                                @endif()  
                    </td> 
                    <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                      <a href="{{URL('admin/trade/detail')}}/{{$chip->id}}">
                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>  
                                &nbsp;
                      <a href="{{URL('admin/trade/delete')}}/{{$chip->id}}">
                                  <i class="fa fa-trash aria-hidden="true""></i>
                                </a>          
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
          <div class="box-main-bottom">
          <div class="box-main-showing">Showing {{$current_page}} to 10 of {{$total_count}} entries</div>
          {{$Trades_manage->links() }}
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