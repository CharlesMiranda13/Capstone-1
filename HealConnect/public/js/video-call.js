const roomName = document.getElementById('room-data').dataset.room;
const userName = document.getElementById('room-data').dataset.user;
const meetingToken = document.getElementById('room-data').dataset.token;

let callFrame;

async function initializeCall() {
    try {
        if (typeof DailyIframe === 'undefined') {
            alert('Video calling library failed to load. Please refresh.');
            return;
        }

        const container = document.getElementById('call-frame');
        if (!container) {
            alert('Video container not found. Please refresh.');
            return;
        }

        // Daily.co frame with built-in controls
        callFrame = window.DailyIframe.createFrame(container, {
            iframeStyle: { 
                width: '100%', 
                height: '100%', 
                border: '0' 
            },
            showLeaveButton: true,           
            showFullscreenButton: true,     
            showLocalVideo: true,            
            showParticipantsBar: true        
        });

        const joinTimeout = setTimeout(() => {
            alert('Connection timeout. Please try again.');
            window.close();
        }, 30000);

        callFrame
            .on('joined-meeting', () => {
                clearTimeout(joinTimeout);
                document.getElementById('loading-screen').style.display = 'none';
            })
            .on('left-meeting', () => {
                clearTimeout(joinTimeout);
                if (callFrame) callFrame.destroy();
                window.close();
            })
            .on('error', (e) => {
                clearTimeout(joinTimeout);
                if (e.action !== 'camera-error') {
                    alert('Call error occurred. Closing window.');
                    window.close();
                }
            });

        const joinUrl = `https://project-healconnect.daily.co/${roomName}`;
        await callFrame.join({
            url: joinUrl,
            userName: userName,
            token: meetingToken || undefined
        });

    } catch (error) {
        console.error('Failed to initialize:', error);
        alert('Failed to join call: ' + error.message);
        window.close();
    }
}

document.addEventListener('DOMContentLoaded', initializeCall);

window.addEventListener('beforeunload', () => {
    if (callFrame) {
        try {
            callFrame.destroy();
        } catch (e) {}
    }
});