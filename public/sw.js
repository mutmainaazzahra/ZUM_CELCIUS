self.addEventListener("push", function (event) {
  let data = { title: "Peringatan", body: "Notifikasi cuaca." };
  if (event.data) {
    data = event.data.json();
  }

  const options = {
    body: data.body,
    icon: "/public/assets/logo.png",
    data: {
      url: data.data.url || "/",
    },
  };

  event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener("notificationclick", function (event) {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data.url));
});
