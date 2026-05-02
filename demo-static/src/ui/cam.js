export async function startCamera(videoEl) {
  if (!videoEl) {
    throw new Error("Elemen video belum siap.");
  }

  if (!navigator.mediaDevices?.getUserMedia) {
    throw new Error("Browser tidak mendukung akses kamera.");
  }

  const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
  videoEl.srcObject = stream;
  await videoEl.play();
  return stream;
}

export function stopCamera(stream) {
  if (!stream) return;
  for (const track of stream.getTracks()) track.stop();
}

export function captureJpeg(videoEl, quality = 0.85) {
  if (!videoEl) {
    throw new Error("Kamera belum siap.");
  }

  const canvas = document.createElement("canvas");
  canvas.width = videoEl.videoWidth || 640;
  canvas.height = videoEl.videoHeight || 480;
  const ctx = canvas.getContext("2d");

  // mirror like selfie camera
  ctx.translate(canvas.width, 0);
  ctx.scale(-1, 1);
  ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

  return canvas.toDataURL("image/jpeg", quality);
}
