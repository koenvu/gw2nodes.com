<div class="modal fade" id="add-node">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="map-title">...</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="megaserver-warning"><strong>Attention!</strong> You previously selected a different map to add a node to, on the same server name. Are you sure you are on the same megaserver IP?</div>
                
                <div class="rich-box"><label><input type="checkbox" id="create-rich"> This is a rich node</label></div>

                <label>Select the node type below.</label>
                <div id="list"></div>

                <label>Add custom notes?</label>
                <input type="text" id="create-notes" class="form-control" placeholder="Custom notes, if necessary">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add-node-button">Save changes</button>
            </div>
        </div>
    </div>
</div>