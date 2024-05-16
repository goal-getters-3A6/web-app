from chatterbot import ChatBot
from chatterbot.trainers import ListTrainer
from cleaner import clean_corpus
from flask import Flask, request, jsonify
app = Flask(__name__)


CORPUS_FILE = "chatttt.txt"

chatbot = ChatBot("Chatpot")

@app.route('/chatbot', methods=['GET'])
def get_response():
    user_input = request.args.get('user_input')
    response = chatbot.get_response(user_input)
    return jsonify({'response': str(response)})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

trainer = ListTrainer(chatbot)
cleaned_corpus = clean_corpus(CORPUS_FILE)
trainer.train(cleaned_corpus)

