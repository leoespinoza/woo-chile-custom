
  
        <!-- Page Content goes here -->
        <div class="row">

			<!-- <div class="input-field col s4">
				<select id="countries-dropdown" name="countries"></select>
				<label>Materialize Select</label>
			</div> -->
			<div class="col s12 summTblDiv">
				<table id="options-datatable" class="display " cellspacing="0" width="100%">
					<thead>
						<tr>
							<td></td>
							<td class="woocfcl-table-footer-checkbox"><label><input type="checkbox" id="select_all" class="filled-in"/><span>Selección</span></label></td>
							<th>ID</th>
							<th>Country</th>
							<th>Name</th>
							<th>AdditionalCode</th>
							<th>NumberCode</th>
							<th>Enabled</th>
							<td></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td></td>
							<td></td>
							<th>ID</th>
							<th>Country</th>
							<th>Name</th>
							<th>AdditionalCode</th>
							<th>NumberCode</th>
							<th>Enabled</th>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
        </div>

        <div id="modal1" class="modal">
            <div class="modal-content">
                <h4>Modal Header</h4>
                <p>A bunch of text</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
            </div>
        </div>
        <!-- Page Content goes here -->
        <div id="EditFieldModal" class="modal">
            <form id="form-state-edit" action="">
                <div class="modal-header">
                    <div class="left  pdg1-l"><h5>Edit Record</h5> </div>
                    <div class="right mrg1-t">
                        <a href="#!" class="modal-action modal-close">
                            <i class="material-icons small">close</i>
                        </a>
                    </div>
                </div>
                <div class="modal-content">
                    <div class="modal-body">
						<input type="hidden" name="RowId" >
                        <input type="hidden" name="RowOrder" >
						<div class="row">
							<div class="form-group col s4">
								<label>Code:</label>
								<input  disabled value="" name="ID" id="edit-ID" type="text" class="validate">
								
							</div>
							<div class="form-group col s4">
								<label>NumberCode: </label>
								<input id="edit-NumberCode"  class="form-control validate" type="number" name="NumberCode" value="" required>
                        	</div>
							<div class="form-group col s4">
								<label>AdditionalCode: </label>
								<input id="edit-AdditionalCode" class="form-control validate" type="text" name="AdditionalCode" value="" required>
                        	</div>
						</div>
						
						<div class="form-group">
                            <label>Name: </label>
                            <input id="edit-Name" class="form-control" type="text" name="Name" value="" required>
                        </div>


						<div class="form-group">
                            <label>Enabled: </label>
							<div class="switch">
								<label>
									No
									<input type="checkbox" id="edit-enabled" name="enabled" >
									<span class="lever"></span>
									Sí
								</label>
							</div>
                        </div>

                    </div>
                
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
                    <button class="btn">Próximo</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary modal-close waves-effect waves-green btn-flat">Close</button>
                    <button class="btn waves-effect waves-light" type="submit" name="action">Submit
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </form>
        </div>       
