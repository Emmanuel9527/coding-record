from sklearn.model_selection import train_test_split
import pandas as pd

data = pd.read_excel(
    r"C:\Users\User\OneDrive\Desktop\應用線性統計模型作業\final\data.xlsx", header=0, sheet_name="Sheet1")
df = data[['Webs_num', 'Backlog', 'Team_exp',
          'Proc_chg', 'X1', 'X2', 'X3', 'X4', 'X5', 'X6', 'X7', 'G1', 'G2', 'G3']]

x = data.drop(columns=['Webs_num', 'X6', 'X7', 'G3'])
y = data['Webs_num']

# 分成5份
x_train, x_test, y_train, y_test = train_test_split(
    x, y, test_size=0.2, random_state=42)

# 查看分類數據大小
print(x_train.shape, y_train.shape)
print(x_test.shape, y_test.shape)

df_train = pd.DataFrame(x_train)
df_train['Webs_num'] = y_train
df_test = pd.DataFrame(x_test)
df_test['Webs_num'] = y_test


excel_file = 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/data_split.xlsx'

with pd.ExcelWriter(excel_file) as writer:
    df_train.to_excel(writer, sheet_name='Train', index=False)
    df_test.to_excel(writer, sheet_name='Test', index=False)
