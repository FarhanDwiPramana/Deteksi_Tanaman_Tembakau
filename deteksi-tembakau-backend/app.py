from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
from PIL import Image
import io
import os

app = Flask(__name__)

MODEL_PATH = os.path.join('model', 'Model Fixed.h5')
model = tf.keras.models.load_model(MODEL_PATH)

CLASS_NAMES = ['A', 'B', 'C']
THRESHOLD = 0.7  # Ambang batas kepercayaan minimal

def preprocess_image(image_bytes):
    image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
    image = image.resize((224, 224))
    image_array = np.array(image) / 255.0
    image_array = np.expand_dims(image_array, axis=0)
    return image_array

@app.route('/predict', methods=['POST'])
def predict():
    if 'image' not in request.files:
        return jsonify({'error': 'No image uploaded'}), 400

    file = request.files['image']
    image_bytes = file.read()

    try:
        input_image = preprocess_image(image_bytes)
        prediction = model.predict(input_image)
        predicted_index = int(np.argmax(prediction[0]))
        confidence = float(np.max(prediction[0]))
        label = CLASS_NAMES[predicted_index]

        # Cek apakah confidence cukup tinggi
        if confidence < THRESHOLD:
            return jsonify({
                'recognized': False,
                'message': 'Gambar tidak dikenali. Pastikan gambar adalah daun tembakau yang valid.',
                'confidence': confidence,
                'raw_scores': prediction[0].tolist()
            }), 200

        return jsonify({
            'recognized': True,
            'class': label,
            'confidence': confidence,
            'raw_scores': prediction[0].tolist()
        }), 200

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
