import openpyxl


def page_worker(page, wb):
    print('PAGE_WORKER:')
    sheet = wb[page]
    names = sheet['A']
    descriptions = sheet['B']
    presents = sheet['C']
    prices = sheet['D']
    categories = sheet['E']
    length = len(names)
    for i in range(0, length):
        try:
            print(names[i].value)
        except:
            print(names[i].value.encode('utf8'))

        try:
            print(descriptions[i].value)
        except:
            print(descriptions[i].value.encode('utf8').decode('ascii'))

        print("--------------------")


wb = openpyxl.load_workbook(filename='/var/www/bbstore.ru/html/spreadsheets/tmp.xlsx')
pages = wb.get_sheet_names()
print(pages)
pages_count = len(pages)
print(pages_count)
for page in pages:
    page_worker(page, wb)



