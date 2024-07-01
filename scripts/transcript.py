# get_transcript.py
import sys
import json
from youtube_transcript_api import YouTubeTranscriptApi

def get_transcript(video_id):
    try:
        transcript = YouTubeTranscriptApi.get_transcript(video_id)
        return transcript
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    video_id = sys.argv[1]
    result = get_transcript(video_id)
    print(json.dumps(result))  # Ensure the output is valid JSON