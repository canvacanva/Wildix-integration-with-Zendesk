# Wildix-integration-with-Zendesk

Ticket creation, update, delete.

### Create
```
set(ticketID=${CURL(http://127.0.0.1/zendesk/t_create.php?num=${CALLERID(num)})})
```

### Update
test.php start another script in background at call ended
```
set(update_ticket_at_call_end=${CURL(http://127.0.0.1/zendesk/test.php?ticketId=${ticketID}&uniqueId=${CALLID}&tag=${ticketID}&num=${CALLERNUM})})
```
