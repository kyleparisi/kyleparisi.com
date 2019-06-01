$(document).ready(() => {
  const socket = io();
  window.data = window.data = {
    loading: true,
    chat: false,
    chats: {},
    text: "",
    scrollTop: 0,
    online: [],
    typing: [],
    location,
    messagePane: false,
    newMessageId: false,
    toggleChatList: true,
    loadedOldestMessage: false
  };

  function getSearchParameters() {
    var prmstr = window.location.search.substr(1);
    return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
  }

  function transformToAssocArray(prmstr) {
    var params = {};
    var prmarr = prmstr.split("&");
    for (var i = 0; i < prmarr.length; i++) {
      var tmparr = prmarr[i].split("=");
      params[tmparr[0]] = tmparr[1];
    }
    return params;
  }

  function scrollMessagePaneIfNeeded() {
    const messagesEl = document.getElementById("messages");
    if (!messagesEl) {
      return;
    }
    const isScrolledBottom =
      messagesEl.scrollHeight - messagesEl.clientHeight <=
      messagesEl.scrollTop + 100;
    if (isScrolledBottom) {
      console.log("Adjust scroll");
      Vue.nextTick(() => (messagesEl.scrollTop = messagesEl.scrollHeight));
    }
  }
  function storeNewMessageFlagIfNeeded(messages) {
    data.newMessageId = false;
    if (!messages || !messages.length) {
      return false;
    }
    // If you sent last message, no new messages
    if (messages[messages.length - 1].user_id === data.chat.you) {
      return false;
    }
    for (let i = 0; i < messages.length; i++) {
      if (
        parseInt(messages[i].timestamp) > data.chat.read &&
        messages[i].user_id !== data.chat.you
      ) {
        data.newMessageId = messages[i].id;
        break;
      }
    }
  }
  function groupMessagesByDay(messages) {
    const currentDate = new Date();
    // set to midnight
    currentDate.setHours(0, 0, 0, 0);
    const byDate = {};
    messages.map(message => {
      const date = new Date(message.timestamp * 1000);
      // set to midnight
      date.setHours(0, 0, 0, 0);
      const time = date.getTime() / 1000;
      if (!byDate[time]) {
        byDate[time] = true;
        message.addAvatar = true;
        message.addDayDivider = time;
      } else {
        message.addDayDivider = false;
      }
    });
  }
  function groupMessagesByUser(messages) {
    messages.reduce((grouped, message) => {
      if (grouped.length === 0) {
        grouped.unshift([message]);
        message.addAvatar = true;
      } else if (grouped[0][0].user_id === message.user_id) {
        grouped[0].push(message);
        if (message.addAvatar !== true) {
          message.addAvatar = false;
        }
      } else {
        grouped.unshift([message]);
        message.addAvatar = true;
      }
      return grouped;
    }, []);
  }
  function groupMessages() {
    const messages = _.get(window, "data.chat.messages", false);
    if (!messages) {
      return false;
    }
    storeNewMessageFlagIfNeeded(messages);
    groupMessagesByDay(messages);
    groupMessagesByUser(messages);
  }
  window.groupMessages = groupMessages;
  const notify = _.debounce(() => {
    window.notifyMe("New chat message.");
  }, 5000);
  function typing() {
    socket.emit("typing", data.chat.key);
  }
  window.typing = typing;

  socket.on("connect", function() {
    Api.chat
      .getChats()
      .then(res => res.data)
      .then(data => {
        Vue.set(window.data, "chats", data);
        const select = getSearchParameters().select;
        if (select) {
          window.data.chat = data[select];
        } else {
          // lazily select first chat
          window.data.chat = data[Object.keys(data)[0]];
        }

        groupMessages();
        window.data.loading = false;
        Vue.nextTick(() => {
          const messagesEl = document.getElementById("messages");
          if (messagesEl) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
          }
        });
      });
  });
  socket.on("message", message => {
    switch (message.method) {
      case "push":
        _.get(window.data, message.path, []).push(message.data);
        break;
      default:
        _[message.method](window.data, message.path, message.data);
    }

    scrollMessagePaneIfNeeded();
    groupMessages();
    if (document.hidden) {
      console.log("Notify user.");
      notify();
    } else {
      console.log("Document not hidden, don't notify");
    }
  });
  socket.on("typing", typing => {
    window.data.typing = typing;
  });
  socket.on("online", users => {
    window.data.online = users;
  });
  window.socket = socket;

  new Vue({el: "#chat"});

  if (!navigator.onLine) {
    window.data.systemError = "You are offline.";
  }
});
