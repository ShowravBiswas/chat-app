export const formatTime = (datetime) => {
    const date = new Date(datetime);
    const now = new Date();

    const isToday = date.toDateString() === now.toDateString();

    const dayOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
    const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

    const diffTime = now - date;
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    const timeString = date.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });

    if (isToday) {
        return timeString;
    } else if (diffDays < 7) {
        const weekday = dayOfWeek[date.getDay()];
        return `${weekday} at ${timeString}`;
    } else {
        const month = monthNames[date.getMonth()];
        const day = date.getDate();
        return `${month} ${day} at ${timeString}`;
    }
};