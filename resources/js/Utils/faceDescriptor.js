import * as faceapi from "face-api.js";

let modelLoadingPromise = null;

export async function loadFaceModels() {
    if (!modelLoadingPromise) {
        modelLoadingPromise = Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
            faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
            faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
        ]);
    }

    return modelLoadingPromise;
}

export async function extractFaceDescriptorFromImage(imageSource) {
    await loadFaceModels();

    const image = await loadImage(imageSource);
    const detection = await faceapi
        .detectSingleFace(image, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.45 }))
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        throw new Error("Wajah tidak terdeteksi. Pastikan wajah terlihat jelas, cukup cahaya, dan menghadap kamera.");
    }

    return Array.from(detection.descriptor).map((value) => Number(value.toFixed(8)));
}

function loadImage(source) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.crossOrigin = "anonymous";
        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error("Gagal membaca gambar wajah."));
        image.src = source;
    });
}
