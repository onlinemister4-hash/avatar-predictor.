import sys, json
from pathlib import Path
from collections import defaultdict

# Используем OpenAI Whisper
try:
    import whisper
except:
    print(json.dumps({"error":"Whisper не установлен"}))
    sys.exit()

file_path = sys.argv[1]

# Загружаем модель
model = whisper.load_model("small")
result = model.transcribe(file_path, language="ru")
text = result['text']

# Список участников (пример)
participants = ["Аврора", "Афродита", "Берегиня", "Берендей", "Золотая рыбка", "Илья Муромец", "Кикимора", "Купидон", "Посейдон", "Финист Ясный сокол", "Хозяйка Медной горы", "Чудо-Юдо"]

# Оценка похвалы/критики
positive_words = ["отлично","хорошо","молодец","красиво","замечательно","сильный","яркий","выразительно"]
negative_words = ["плохо","слабо","недостаточно","плохо","ошибка","неудачно"]

scores = defaultdict(lambda: {"выбытие":0,"финал":0})

for p in participants:
    if p in text:
        for w in positive_words:
            if w in text:
                scores[p]["финал"] += 1
        for w in negative_words:
            if w in text:
                scores[p]["выбытие"] += 1

# Превращаем в проценты
total_pos = sum([v["финал"] for v in scores.values()]) or 1
total_neg = sum([v["выбытие"] for v in scores.values()]) or 1

for p in scores:
    scores[p]["шанс_финала"] = round(scores[p]["финал"]/total_pos*100,1)
    scores[p]["шанс_вылета"] = round(scores[p]["выбытие"]/total_neg*100,1)

print(json.dumps(scores, ensure_ascii=False))
