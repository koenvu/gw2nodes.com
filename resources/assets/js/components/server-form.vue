<style>
.Notice {
  font-style: italic;
  color: red;
}

</style>

<template>
	<form action="/main/server" id="changeserverform">
        <div class="modal-header">
            <h4 class="modal-title">Pick your server</h4>
        </div>
        <div class="modal-body">
            <input type="text" name="id" id="serverselect" placeholder="Server IP or custom map name" class="form-control" v-model="ip">
            <div class="Notice" v-show="ip.indexOf(':0') > -1">Note: You don't have to add :0</div>
            <div class="Notice" v-show="!isIp">Most people will use the server IP (<strong>/ip</strong> in-game) to add nodes to a map.</div>
            
            <br>

            <p class="lead">GW2Nodes is a community driven node map. You can use the chat command <em>/ip</em> in-game to find out which server you are currently on. You can also use a custom map name and share it with friends.</p>
            <p class="lead"><strong>Note:</strong> the server IP is likely to change as you switch maps!</p>
        </div>
        <div class="modal-footer">
            <input type="submit" name="submit" v-model="submitButton" :disabled="!ip.length" id="serverselectbutton" :class="{ btn: true, 'btn-primary': ip.length, 'btn-default': !ip.length }">
        </div>
    </form>
</template>

<script>
	export default {
		data() {
			return {
				ip: ''
			}
		},

        computed: {
            submitButton() {
                return this.ip.length > 0 ? 'Confirm' : 'Choose an IP first';
            },

            isIp() {
                var pattern = /^[0-9.]*$/;
                return pattern.test(this.ip);
            }
        }
	}
</script>