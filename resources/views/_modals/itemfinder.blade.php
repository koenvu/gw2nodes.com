<div class="modal fade" id="itemfinder-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Item Finder</h4>
            </div>
            <div class="modal-body">
                <p class="help-block">Click on an item below to search for it on your characters and bank. <em>Requires API Key set in Preferences.</em></p>
                <div id="ioi-results"></div>
                <div id='sidebar-items'>
                    @foreach ($itemsoi as $category => $iois)
                        <h4>{{ $category }}</h4>
                        @foreach ($iois as $ioi)
                            <div class='item-entry' data-key='{{ $ioi->item->api_id }}'>
                                <img src='{{ $ioi->item->image_url }}' class='node-icon'>
                                <span class='node-title'>{{ $ioi->item->name }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" name="submit" value="Close" data-dismiss="modal" class="btn btn-default">
            </div>
        </div>
    </div>
</div>