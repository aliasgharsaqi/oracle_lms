@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
@endpush

@section('content')
<button id="chat-fab" class="fixed bottom-8 right-8 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition-transform transform hover:scale-110">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 00-1-1H5a1 1 0 00-1 1v5h12V6zM2 15a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z" />
        <path d="M8 8a1 1 0 100-2 1 1 0 000 2z" />
    </svg>
</button>

<div id="chat-window" class="fixed bottom-24 right-8 w-96 h-[600px] bg-white rounded-xl shadow-2xl flex flex-col transition-all duration-300 ease-out transform translate-y-10 opacity-0 hidden">
    
    <header class="bg-blue-600 text-white p-4 flex items-center gap-4 rounded-t-xl">
        <div class="relative">
            <img src="https://via.placeholder.com/40" alt="Bot Avatar" class="w-10 h-10 rounded-full">
            <span class="absolute bottom-0 right-0 block h-3 w-3 bg-green-400 rounded-full border-2 border-blue-600"></span>
        </div>
        <div>
            <h3 class="font-semibold text-lg">LMS Assistant</h3>
            <p class="text-sm text-blue-100">Online</p>
        </div>
    </header>

    <main id="chat-messages" class="flex-grow p-4 space-y-4 overflow-y-auto bg-gray-50">
        </main>

    <div id="typing-indicator" class="p-4 hidden">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-150"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-300"></div>
        </div>
    </div>
    
    <div id="quick-replies" class="p-2 border-t bg-white">
        </div>


    <footer class="p-4 border-t bg-white rounded-b-xl">
        <form id="chat-form" class="flex items-center gap-2">
            <input type="text" id="chat-input" placeholder="Type your message..." class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
            <button type="submit" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.428A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
            </button>
        </form>
    </footer>

</div>
@endsection

@push('scripts')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- DOM ELEMENT REFERENCES ---
    const chatFab = document.getElementById('chat-fab');
    const chatWindow = document.getElementById('chat-window');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const typingIndicator = document.getElementById('typing-indicator');
    const quickRepliesContainer = document.getElementById('quick-replies');


    // --- BOT LOGIC & DATA ---
    // In a real app, you'd use a service like Dialogflow or your own backend NLP
    const responses = {
        "hello": "Hi there! How can I help you today?",
        "hi": "Hi there! How can I help you today?",
        "assignments": "You can view your assignments by going to the 'My Courses' section and selecting the 'Assignments' tab.",
        "grades": "Your grades are available under the 'Gradebook' section in the main menu. Would you like a link?",
        "schedule": "Your class schedule for today, September 16, 2025, is available on your dashboard. You have Chemistry at 1:00 PM.",
        "password": "You can reset your password by clicking on your profile icon and selecting 'Account Settings'.",
        "default": "I'm sorry, I don't understand that. Could you please rephrase? You can ask about 'assignments', 'grades', 'schedule', or 'password'."
    };

    const quickReplies = ["Assignments", "Grades", "Schedule"];


    // --- FUNCTIONS ---

    // Toggles the chat window's visibility
    const toggleChatWindow = () => {
        if (chatWindow.classList.contains('hidden')) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('opacity-0', 'translate-y-10');
            }, 10);
            addWelcomeMessage();
        } else {
            chatWindow.classList.add('opacity-0', 'translate-y-10');
            setTimeout(() => {
                chatWindow.classList.add('hidden');
            }, 300);
        }
    };

    // Adds a message to the chat log
    const addMessage = (text, sender) => {
        const messageClass = sender === 'bot' 
            ? 'flex gap-3 items-start' 
            : 'flex flex-row-reverse gap-3 items-start';

        const bubbleClass = sender === 'bot'
            ? 'bg-gray-200 text-gray-800 rounded-r-lg rounded-bl-lg'
            : 'bg-blue-600 text-white rounded-l-lg rounded-br-lg';
            
        const messageElement = `
            <div class="${messageClass}">
                <div class="w-10 h-10 rounded-full bg-gray-300 flex-shrink-0"></div>
                <div class="max-w-xs p-3 ${bubbleClass}">
                    <p class="text-sm">${text}</p>
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight; // Auto-scroll to bottom
    };

    // Generates and displays quick reply buttons
    const showQuickReplies = (replies) => {
        quickRepliesContainer.innerHTML = '';
        const buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'flex flex-wrap gap-2 justify-center p-2';

        replies.forEach(reply => {
            const button = document.createElement('button');
            button.textContent = reply;
            button.className = 'quick-reply-btn bg-white border border-blue-500 text-blue-500 text-sm font-semibold py-1 px-3 rounded-full hover:bg-blue-50';
            buttonsContainer.appendChild(button);
        });
        quickRepliesContainer.appendChild(buttonsContainer);
    };


    // Simulates the bot's response
    const handleBotResponse = (userInput) => {
        typingIndicator.classList.remove('hidden');
        quickRepliesContainer.innerHTML = ''; // Hide quick replies

        setTimeout(() => {
            const lowerCaseInput = userInput.toLowerCase();
            let botReply = responses.default;

            for (const key in responses) {
                if (lowerCaseInput.includes(key)) {
                    botReply = responses[key];
                    break;
                }
            }
            
            typingIndicator.classList.add('hidden');
            addMessage(botReply, 'bot');
            showQuickReplies(quickReplies);

        }, 1500); // Simulate bot thinking time
    };

    // Adds the initial welcome message
    const addWelcomeMessage = () => {
        // Only add if the chat is empty
        if (chatMessages.children.length === 0) {
            addMessage("Hello! I'm the LMS Assistant. How can I help you today?", 'bot');
            showQuickReplies(quickReplies);
        }
    };

    // Handles user input from form or quick replies
    const handleUserInput = (text) => {
        if (!text.trim()) return;
        
        addMessage(text, 'user');
        chatInput.value = ''; // Clear input field
        handleBotResponse(text);
    };


    // --- EVENT LISTENERS ---
    chatFab.addEventListener('click', toggleChatWindow);

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        handleUserInput(chatInput.value);
    });

    quickRepliesContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('quick-reply-btn')) {
            handleUserInput(e.target.textContent);
        }
    });

});
</script>
@endpush
@endpush