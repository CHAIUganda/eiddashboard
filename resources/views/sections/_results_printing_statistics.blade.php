 <div class="row">
    

    <div class="col-lg-12 facilties-sect " >
    
        <div>
            <span style="font-size: 10px; color: #F44336;">
            {{env('UPDATE_MESSAGE')}} {{env('RESULTS_CUTOFF')}}
            </span>
            <br>
            <br>
            
        </div>
        
             <table  id="results-stats-table" datatable="ng" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Facility</th>  
                                    <th>Hub</th>                   
                                    <th>Implementing Partner</th>
                                    <th># Pending Results</th>

                                    
                                    <th>Last Printed on</th>
                                    <th>Oldest Result Pending Printing(in Days)</th>

                                </tr>
                            </thead>
                               <tbody>                                
                                    <tr ng-repeat="f in facilities_array" ng-if="f.pending_results > 0">
                                        <td class="ng-cloak"><% f.facility %></td>
                                        <td class="ng-cloak"><% f.hub %></td>
                                        <td class="ng-cloak"><% f.ip %></td>
                                        <td class="ng-cloak"><%f.pending_results%></td>

                                        
                                        <td class="ng-cloak"><%f.last_printed_on %></td>
                                        <td class="ng-cloak"><% generateDaysDifference(f.oldest_result_pending_printing) %></td>
                                    </tr>                        
                                 </tbody>
                             
            </table>

            

    </div>
</div>