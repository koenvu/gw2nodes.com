<div class="modal fade" id="preferences-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Preferences</h4>
            </div>
            <div class="modal-body">
                <p class="help-block">Preferences are stored in your browsers 'local storage' using JavaScript. If preferences are not persisted, there is something wrong with that storage.</p>

                <h4>Show waypoints</h4>
                <input type="checkbox" id="waypoints-pref">

                <h4>Show Points of Interest</h4>
                <input type="checkbox" id="landmarks-pref">
                
                <h4>Show chatcodes on click</h4>
                <input type="checkbox" id="chatcodes-pref">

                <h4>GW2 API Key</h4>
                <input type="text" id="api-key" class="form-control">
                <p class="help-block">Used for the item finder and possibly future functions.</p>
            </div>
            <div class="modal-footer">
                <input type="submit" name="submit" value="Close" data-dismiss="modal" class="btn btn-default">
            </div>
        </div>
    </div>
</div>