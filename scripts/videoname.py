# get_video_name.py
from googleapiclient.discovery import build
import sys
import json

def get_video_name(video_id, api_key):
    youtube = build('youtube', 'v3', developerKey=api_key)

    request = youtube.videos().list(
        part="snippet",
        id=video_id
    )
    response = request.execute()

    if 'items' in response and len(response['items']) > 0:
        video_title = response['items'][0]['snippet']['title']
        return {"title": video_title}
    else:
        return {"error": "Video not found"}

if __name__ == "__main__":
    video_id = sys.argv[1]
    api_key = sys.argv[2]
    result = get_video_name(video_id, api_key)
    print(json.dumps(result))